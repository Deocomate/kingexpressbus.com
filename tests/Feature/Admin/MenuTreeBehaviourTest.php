<?php

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('cascade-deletes children when a parent menu is deleted', function () {
    $parent = Menu::factory()->create(['parent_id' => Menu::ROOT_PARENT_ID, 'priority' => 10]);
    $child = Menu::factory()->create(['parent_id' => $parent->id, 'priority' => 5]);
    $grandchild = Menu::factory()->create(['parent_id' => $child->id, 'priority' => 3]);

    $parent->delete();

    expect(Menu::find($child->id))->toBeNull()
        ->and(Menu::find($grandchild->id))->toBeNull();
});

it('assigns max priority + 1 when creating a menu in an existing group', function () {
    $parent = Menu::factory()->create(['parent_id' => Menu::ROOT_PARENT_ID, 'priority' => 1]);
    Menu::factory()->create(['parent_id' => $parent->id, 'priority' => 1]);
    Menu::factory()->create(['parent_id' => $parent->id, 'priority' => 2]);
    Menu::factory()->create(['parent_id' => $parent->id, 'priority' => 3]);

    // Creating without explicit priority triggers the boot hook to assign max+1.
    $new = Menu::factory()->create(['parent_id' => $parent->id, 'priority' => 0]);

    expect($new->fresh()->priority)->toBe(4);
});

it('normalises missing parent_id to ROOT_PARENT_ID (-1)', function () {
    // Create without setting parent_id (boot hook should normalise null/0 → -1).
    $menu = new Menu([
        'name' => 'Root Item',
        'type' => 'custom_link',
        'url' => 'https://example.com',
        'priority' => 99,
    ]);
    // Leave parent_id unset so it defaults to null.
    $menu->save();

    expect($menu->fresh()->parent_id)->toBe(Menu::ROOT_PARENT_ID);
});

it('isRoot() returns true for root-level menus and false for children', function () {
    $root = Menu::factory()->create(['parent_id' => Menu::ROOT_PARENT_ID, 'priority' => 1]);
    $child = Menu::factory()->create(['parent_id' => $root->id, 'priority' => 1]);

    expect($root->isRoot())->toBeTrue()
        ->and($child->isRoot())->toBeFalse();
});
