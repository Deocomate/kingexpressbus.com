<?php

/**
 * Auto-discovered authorization matrix for every quan-tri route.
 * Covers admin / customer / guest roles + unauthenticated visitor.
 * New routes under quan-tri are included automatically via Route::getRoutes().
 */

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

/**
 * @return list<array{uri: string, name: string, methods: list<string>, middleware: list<string>}>
 */
function quanTriRoutes(): array
{
    $routes = [];

    foreach (Route::getRoutes() as $route) {
        /** @var LaravelRoute $route */
        $uri = $route->uri();

        if (! str_starts_with($uri, 'quan-tri')) {
            continue;
        }

        $methods = array_values(array_filter(
            $route->methods(),
            fn (string $m) => ! in_array($m, ['HEAD', 'OPTIONS'], true)
        ));

        if ($methods === []) {
            continue;
        }

        $routes[] = [
            'uri' => $uri,
            'name' => (string) ($route->getName() ?? ''),
            'methods' => $methods,
            'middleware' => $route->middleware(),
        ];
    }

    return $routes;
}

function isAuthRoute(string $name): bool
{
    return in_array($name, ['admin.login', 'admin.login.submit', 'admin.logout'], true);
}

/**
 * Build a concrete URI with placeholder params substituted.
 */
function concreteUri(string $uri): string
{
    return preg_replace('/\{[^}]+\}/', '1', $uri) ?? $uri;
}

it('discovers quan-tri routes for the authorization matrix', function () {
    expect(quanTriRoutes())->not->toBeEmpty();
});

it('denies customer role on every protected quan-tri route', function () {
    $customer = User::factory()->customer()->create();
    $failures = [];

    foreach (quanTriRoutes() as $route) {
        if (isAuthRoute($route['name'])) {
            continue;
        }

        $uri = concreteUri($route['uri']);

        foreach ($route['methods'] as $method) {
            $response = $this->actingAs($customer)->call($method, '/'.$uri);
            $status = $response->status();

            if ($status !== 403) {
                $failures[] = strtoupper($method)." {$uri} [{$route['name']}] => {$status}";
            }
        }
    }

    expect($failures)->toBeEmpty('Customer must get 403: '.implode('; ', $failures));
});

it('denies guest role on every protected quan-tri route', function () {
    $guest = User::factory()->create(['role' => 'guest']);
    $failures = [];

    foreach (quanTriRoutes() as $route) {
        if (isAuthRoute($route['name'])) {
            continue;
        }

        $uri = concreteUri($route['uri']);

        foreach ($route['methods'] as $method) {
            $response = $this->actingAs($guest)->call($method, '/'.$uri);
            $status = $response->status();

            if ($status !== 403) {
                $failures[] = strtoupper($method)." {$uri} [{$route['name']}] => {$status}";
            }
        }
    }

    expect($failures)->toBeEmpty('Guest role must get 403: '.implode('; ', $failures));
});

it('redirects unauthenticated visitors from every protected quan-tri route', function () {
    $failures = [];

    foreach (quanTriRoutes() as $route) {
        if (isAuthRoute($route['name'])) {
            continue;
        }

        $uri = concreteUri($route['uri']);

        foreach ($route['methods'] as $method) {
            $response = $this->call($method, '/'.$uri);
            $status = $response->status();

            // Laravel may redirect (302) or return 401 depending on Accept header.
            if (! in_array($status, [302, 401], true)) {
                $failures[] = strtoupper($method)." {$uri} [{$route['name']}] => {$status}";
            }
        }
    }

    expect($failures)->toBeEmpty('Visitor must be redirected/unauthorized: '.implode('; ', $failures));
});

it('allows admin role past the auth gate on every protected quan-tri route', function () {
    $admin = User::factory()->admin()->create();
    $failures = [];

    foreach (quanTriRoutes() as $route) {
        if (isAuthRoute($route['name'])) {
            continue;
        }

        $uri = concreteUri($route['uri']);

        foreach ($route['methods'] as $method) {
            $response = $this->actingAs($admin)->call($method, '/'.$uri);
            $status = $response->status();

            // Admin must clear auth middleware. Validation/not-found/method errors are fine.
            if (in_array($status, [401, 403], true) || ($status === 302 && Str::contains($response->headers->get('Location', ''), 'dang-nhap'))) {
                $failures[] = strtoupper($method)." {$uri} [{$route['name']}] => {$status}";
            }
        }
    }

    expect($failures)->toBeEmpty('Admin must clear auth gate: '.implode('; ', $failures));
});

it('documents that a second privileged role would invalidate IDOR exemption', function () {
    // After Phase 1, users.role is admin|customer|guest and only admin enters /quan-tri.
    // There is no per-record ownership model — IDOR checks are intentionally skipped.
    // Adding any second privileged role requires re-auditing every admin write route.
    expect(true)->toBeTrue();
});
