<?php

/**
 * Phase 9 security gate coverage: mass-assignment, injection whitelist,
 * CSRF token presence on write forms, XSS sink, transaction middleware coverage,
 * and multi-step rollback for Bus sync / menu reorder / default profile switch.
 */

use App\Http\Middleware\AdminDatabaseTransaction;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\BusService;
use App\Models\Menu;
use App\Models\User;
use App\Models\WebProfile;
use App\Support\Admin\HtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

// ─── Authz middleware presence (group 1 complement) ──────────────────────────

it('every non-auth quan-tri route has admin.auth middleware', function () {
    $violations = [];

    foreach (Route::getRoutes() as $route) {
        $uri = $route->uri();
        if (! str_starts_with($uri, 'quan-tri')) {
            continue;
        }

        $name = $route->getName() ?? '';
        if (in_array($name, ['admin.login', 'admin.login.submit', 'admin.logout'], true)) {
            continue;
        }

        if (! in_array('admin.auth', $route->middleware(), true)) {
            $violations[] = "{$uri} ({$name})";
        }
    }

    expect($violations)->toBeEmpty(implode(', ', $violations));
});

// ─── CSRF: write forms include token (group 2) ───────────────────────────────

it('admin write forms include csrf token fields', function () {
    $admin = User::factory()->admin()->create();

    $pages = [
        route('admin.login'),
        route('admin.buses.create'),
        route('admin.bookings.create'),
        route('admin.routes.create'),
        route('admin.trips.create'),
        route('admin.surcharges.create'),
        route('admin.website.profiles.create'),
    ];

    foreach ($pages as $url) {
        $response = str_contains($url, 'dang-nhap')
            ? $this->get($url)
            : $this->actingAs($admin)->get($url);

        $response->assertSuccessful();
        expect($response->getContent())->toContain('name="_token"');
    }
});

// ─── Mass-assignment (group 3) ───────────────────────────────────────────────

it('controllers do not call request->all or fill(request) for mass assignment', function () {
    $hits = [];
    $dir = app_path('Http/Controllers/Admin');

    foreach (File::allFiles($dir) as $file) {
        $contents = $file->getContents();
        if (preg_match('/->fill\(\s*\$request\s*\)|::create\(\s*\$request->all\(\s*\)|::update\(\s*\$request->all\(\s*\)/', $contents)) {
            $hits[] = $file->getRelativePathname();
        }
    }

    expect($hits)->toBeEmpty(implode(', ', $hits));
});

it('booking status payment_status confirmed_at payment_transaction_id are not fillable', function () {
    $booking = new Booking;

    expect($booking->getFillable())->not->toContain('status')
        ->and($booking->getFillable())->not->toContain('payment_status')
        ->and($booking->getFillable())->not->toContain('confirmed_at')
        ->and($booking->getFillable())->not->toContain('payment_transaction_id');
});

// ─── XSS (group 4) ───────────────────────────────────────────────────────────

it('raw blade escapes in admin views are limited to safe sinks', function () {
    $allowed = [
        'resources/views/admin/components/display/detail-list.blade.php', // HtmlString only
        'resources/views/admin/partials/sidebar.blade.php', // hardcoded Navigation SVG paths
    ];

    $hits = [];

    foreach (File::allFiles(resource_path('views/admin')) as $file) {
        $relative = str_replace('\\', '/', $file->getRelativePathname());
        $path = 'resources/views/admin/'.$relative;

        if (! str_contains($file->getContents(), '{!!')) {
            continue;
        }

        if (! in_array($path, $allowed, true)) {
            $hits[] = $path;
        }
    }

    expect($hits)->toBeEmpty('Unexpected {!! sinks: '.implode(', ', $hits));
});

it('public page content sink strips script tags via HtmlSanitizer', function () {
    $dirty = '<p>Hello</p><script>alert(1)</script>';
    $clean = HtmlSanitizer::sanitize($dirty);

    expect($clean)->toContain('Hello')
        ->and($clean)->not->toContain('<script');
});

// ─── Injection via sort whitelist (group 6) ──────────────────────────────────

it('rejects unknown sort columns and sql injection payloads on bookings index', function () {
    $admin = User::factory()->admin()->create();
    Booking::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.bookings.index', [
            'sort' => '1; DROP TABLE bookings--',
            'direction' => 'asc',
        ]))
        ->assertSuccessful();

    expect(DB::getSchemaBuilder()->hasTable('bookings'))->toBeTrue();

    $this->actingAs($admin)
        ->get(route('admin.bookings.index', [
            'sort' => 'password',
            'direction' => 'asc',
        ]))
        ->assertSuccessful();
});

// ─── Transaction middleware coverage (group / step 5) ────────────────────────

it('every write route outside auth and ui groups has admin.transaction', function () {
    $violations = [];

    foreach (Route::getRoutes() as $route) {
        $uri = $route->uri();
        if (! str_starts_with($uri, 'quan-tri')) {
            continue;
        }

        $name = $route->getName() ?? '';
        if (in_array($name, ['admin.login', 'admin.login.submit', 'admin.logout'], true)) {
            continue;
        }

        // ui.php group intentionally omits transactions (upload/options I/O).
        if (str_starts_with($name, 'admin.api.') || $name === 'admin.ui-kit') {
            continue;
        }

        $methods = array_values(array_filter(
            $route->methods(),
            fn (string $m) => in_array($m, ['POST', 'PUT', 'PATCH', 'DELETE'], true)
        ));

        if ($methods === []) {
            continue;
        }

        if (! in_array('admin.transaction', $route->middleware(), true)) {
            $violations[] = "{$uri} ({$name})";
        }
    }

    expect($violations)->toBeEmpty(implode(', ', $violations));
});

// ─── Multi-step rollback flows ───────────────────────────────────────────────

it('rolls back bus create plus service sync when an exception fires mid-transaction', function () {
    $service = BusService::factory()->create();
    $middleware = new AdminDatabaseTransaction;
    $request = Request::create('/quan-tri/doi-xe', 'POST');

    try {
        $middleware->handle($request, function () use ($service) {
            $bus = Bus::query()->create([
                'name' => 'Rollback Bus',
                'model_name' => 'Cabin',
                'seat_count' => 20,
                'priority' => 1,
                'content' => '',
            ]);
            $bus->services()->sync([$service->id]);

            expect($bus->services()->count())->toBe(1);

            throw new RuntimeException('forced failure after sync');
        });
    } catch (RuntimeException) {
        // expected
    }

    expect(Bus::where('name', 'Rollback Bus')->exists())->toBeFalse()
        ->and(DB::table('bus_bus_service')->where('bus_service_id', $service->id)->exists())->toBeFalse();
});

it('rolls back menu reorder writes when an exception fires mid-transaction', function () {
    $a = Menu::factory()->create(['name' => 'A', 'priority' => 2]);
    $b = Menu::factory()->create(['name' => 'B', 'priority' => 1]);
    $original = [$a->id => $a->priority, $b->id => $b->priority];

    $middleware = new AdminDatabaseTransaction;
    $request = Request::create('/quan-tri/cau-hinh-website/menus/reorder', 'POST');

    try {
        $middleware->handle($request, function () use ($a, $b) {
            $a->update(['priority' => 10]);
            $b->update(['priority' => 20]);
            throw new RuntimeException('forced reorder failure');
        });
    } catch (RuntimeException) {
        // expected
    }

    expect($a->fresh()->priority)->toBe($original[$a->id])
        ->and($b->fresh()->priority)->toBe($original[$b->id]);
});

it('rolls back default profile demotion when an exception fires mid-transaction', function () {
    $default = WebProfile::factory()->default()->create(['profile_name' => 'Default']);
    $other = WebProfile::factory()->create(['profile_name' => 'Other', 'is_default' => false]);

    $middleware = new AdminDatabaseTransaction;
    $request = Request::create('/quan-tri/cau-hinh-website/profiles/'.$other->id.'/set-default', 'POST');

    try {
        $middleware->handle($request, function () use ($other) {
            // Mirrors WebProfile::booted demotion + promote inside one transaction.
            WebProfile::query()->where('id', '!=', $other->id)->update(['is_default' => false]);
            $other->update(['is_default' => true]);
            throw new RuntimeException('forced set-default failure');
        });
    } catch (RuntimeException) {
        // expected
    }

    expect($default->fresh()->is_default)->toBeTrue()
        ->and($other->fresh()->is_default)->toBeFalse();
});
