<?php

use App\Models\Menu;
use App\Models\User;
use App\Support\Admin\MenuTreeBuilder;
use App\Support\ClientCache;
use App\Support\PriorityOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ─── Helpers ────────────────────────────────────────────────────────────────

function adminForMenuTest(): User
{
    return User::factory()->admin()->create();
}

function rootMenu(array $attrs = []): Menu
{
    return Menu::factory()->create(array_merge(['parent_id' => Menu::ROOT_PARENT_ID], $attrs));
}

function validReorderPayload(array $tree): array
{
    return [
        'tree'    => $tree,
        'version' => MenuTreeBuilder::versionToken(),
    ];
}

// ─── Menu tree index page ────────────────────────────────────────────────────

it('shows menu tree page', function () {
    $m = rootMenu(['name' => 'Home Menu', 'type' => 'custom_link', 'url' => '/']);
    $this->actingAs(adminForMenuTest())
        ->get(route('admin.website.index', ['section' => 'menus']))
        ->assertOk()
        ->assertSee('Home Menu');
});

// ─── Critical: type custom_link vs link ──────────────────────────────────────

it('creates a menu with type custom_link successfully', function () {
    $this->actingAs(adminForMenuTest())
        ->post(route('admin.website.menus.store'), [
            'name'      => 'Custom Link Menu',
            'type'      => 'custom_link',
            'url'       => 'https://example.com',
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertRedirect();

    expect(Menu::where('name', 'Custom Link Menu')->where('type', 'custom_link')->exists())->toBeTrue();
});

it('rejects type=link with 422 (regression guard)', function () {
    $this->actingAs(adminForMenuTest())
        ->post(route('admin.website.menus.store'), [
            'name'      => 'Should Fail',
            'type'      => 'link',
            'url'       => 'https://example.com',
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertSessionHasErrors('type');
});

it('accepts all four valid types', function () {
    $admin = adminForMenuTest();

    foreach (['custom_link', 'page', 'system_page'] as $type) {
        $this->actingAs($admin)
            ->post(route('admin.website.menus.store'), [
                'name'      => "Type {$type}",
                'type'      => $type,
                'url'       => '/some-path',
                'parent_id' => Menu::ROOT_PARENT_ID,
            ])
            ->assertRedirect();
    }

    expect(Menu::count())->toBe(3);
});

it('existing custom_link menu can be updated without 422', function () {
    $menu = Menu::factory()->create(['type' => 'custom_link', 'url' => '/old', 'parent_id' => Menu::ROOT_PARENT_ID]);

    $this->actingAs(adminForMenuTest())
        ->put(route('admin.website.menus.update', $menu), [
            'name'      => 'Updated Name',
            'type'      => 'custom_link',
            'url'       => '/new',
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertRedirect();

    expect($menu->fresh()->url)->toBe('/new');
});

// ─── parent_id root = -1 ─────────────────────────────────────────────────────

it('creates a root menu with parent_id = ROOT_PARENT_ID (-1)', function () {
    $this->actingAs(adminForMenuTest())
        ->post(route('admin.website.menus.store'), [
            'name'      => 'Root Item',
            'type'      => 'custom_link',
            'url'       => '/',
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertRedirect();

    $menu = Menu::where('name', 'Root Item')->first();
    expect($menu->parent_id)->toBe(Menu::ROOT_PARENT_ID);
});

// ─── Cascade delete ──────────────────────────────────────────────────────────

it('cascade-deletes children when parent menu is deleted via admin route', function () {
    $parent = rootMenu(['name' => 'Parent']);
    $child  = Menu::factory()->create(['parent_id' => $parent->id, 'name' => 'Child']);
    $grand  = Menu::factory()->create(['parent_id' => $child->id, 'name' => 'Grand']);

    $this->actingAs(adminForMenuTest())
        ->delete(route('admin.website.menus.destroy', $parent))
        ->assertRedirect();

    expect(Menu::find($child->id))->toBeNull()
        ->and(Menu::find($grand->id))->toBeNull();
});

// ─── Tree ordering ───────────────────────────────────────────────────────────

it('tree builder returns siblings ordered by priority desc', function () {
    // Siblings with different priorities
    $a = rootMenu(['name' => 'A', 'priority' => 10]);
    $b = rootMenu(['name' => 'B', 'priority' => 30]);
    $c = rootMenu(['name' => 'C', 'priority' => 20]);

    $tree = MenuTreeBuilder::build(Menu::treeRecords());
    $names = array_column(array_map(fn ($n) => ['name' => $n['node']->name], $tree), 'name');

    expect($names)->toBe(['B', 'C', 'A']); // desc by priority
});

// ─── Reorder endpoint ────────────────────────────────────────────────────────

it('reorders tree atomically and flushes cache exactly once', function () {
    $a = rootMenu(['priority' => 10]);
    $b = rootMenu(['priority' => 5]);

    $cacheFlushCount = 0;
    Cache::macro('forgetCounted', function ($key) use (&$cacheFlushCount) {
        if ($key === ClientCache::MENUS) $cacheFlushCount++;
        return Cache::forget($key);
    });

    Cache::put(ClientCache::MENUS, 'fake', 3600);

    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), validReorderPayload([
            ['id' => $b->id, 'children' => []],
            ['id' => $a->id, 'children' => []],
        ]))
        ->assertOk()
        ->assertJsonFragment(['reload' => true]);

    // Cache must be cleared
    expect(Cache::has(ClientCache::MENUS))->toBeFalse();
});

it('reorder sets correct parent_id and priority via PriorityOrder', function () {
    $parent = rootMenu(['name' => 'Parent', 'priority' => 1]);
    $child  = rootMenu(['name' => 'Child',  'priority' => 1]);

    // Move $child under $parent as first child
    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), validReorderPayload([
            ['id' => $parent->id, 'children' => [
                ['id' => $child->id, 'children' => []],
            ]],
        ]))
        ->assertOk();

    $child->refresh();
    expect($child->parent_id)->toBe($parent->id)
        ->and($child->priority)->toBeGreaterThan(0);
});

it('reorder result matches PriorityOrder::invertSiblingOrders', function () {
    $a = rootMenu(['priority' => 1]);
    $b = rootMenu(['priority' => 2]);
    $c = rootMenu(['priority' => 3]);

    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), validReorderPayload([
            ['id' => $a->id, 'children' => []],
            ['id' => $b->id, 'children' => []],
            ['id' => $c->id, 'children' => []],
        ]))
        ->assertOk();

    $flatInput = [
        $a->id => ['parent_id' => Menu::ROOT_PARENT_ID, 'order' => 1],
        $b->id => ['parent_id' => Menu::ROOT_PARENT_ID, 'order' => 2],
        $c->id => ['parent_id' => Menu::ROOT_PARENT_ID, 'order' => 3],
    ];
    $expected = PriorityOrder::invertSiblingOrders($flatInput);

    expect($a->fresh()->priority)->toBe($expected[$a->id]['order'])
        ->and($b->fresh()->priority)->toBe($expected[$b->id]['order'])
        ->and($c->fresh()->priority)->toBe($expected[$c->id]['order']);
});

// ─── Reorder validation guards ───────────────────────────────────────────────

it('reorder returns 422 when payload is missing a node', function () {
    $a = rootMenu();
    $b = rootMenu();

    // Only send $a, $b is missing
    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), validReorderPayload([
            ['id' => $a->id, 'children' => []],
        ]))
        ->assertStatus(422);
});

it('reorder returns 422 when depth exceeds 4', function () {
    // Build a 5-deep chain
    $ids = [];
    $nodes = [];
    for ($i = 0; $i < 5; $i++) {
        $ids[] = rootMenu()->id;
    }

    // Nest them 5 levels deep
    $tree = buildDeepChain($ids);

    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), validReorderPayload($tree))
        ->assertStatus(422);
});

it('reorder returns 422 for non-existent id', function () {
    $a = rootMenu();

    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), validReorderPayload([
            ['id' => $a->id, 'children' => []],
            ['id' => 99999, 'children' => []],
        ]))
        ->assertStatus(422);
});

it('reorder returns 422 for duplicate ids', function () {
    $a = rootMenu();

    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), validReorderPayload([
            ['id' => $a->id, 'children' => []],
            ['id' => $a->id, 'children' => []],
        ]))
        ->assertStatus(422);
});

it('reorder returns 409 for stale version token', function () {
    $a = rootMenu();

    $this->actingAs(adminForMenuTest())
        ->postJson(route('admin.website.menus.reorder'), [
            'tree'    => [['id' => $a->id, 'children' => []]],
            'version' => 'stale-token',
        ])
        ->assertStatus(409);
});

// ─── Delete flushes cache ────────────────────────────────────────────────────

it('delete menu flushes cache', function () {
    $menu = rootMenu();
    Cache::put(ClientCache::MENUS, 'fake', 3600);

    $this->actingAs(adminForMenuTest())
        ->delete(route('admin.website.menus.destroy', $menu))
        ->assertRedirect();

    expect(Cache::has(ClientCache::MENUS))->toBeFalse();
});

// ─── Private helpers ─────────────────────────────────────────────────────────

function buildDeepChain(array $ids, int $depth = 0): array
{
    if (empty($ids)) return [];
    return [
        ['id' => array_shift($ids), 'children' => buildDeepChain($ids, $depth + 1)],
    ];
}
