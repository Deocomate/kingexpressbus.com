<?php

use App\Models\Menu;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('menu form validation requires URL when type is custom_link', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.website.menus.store'), [
            'name' => 'Custom Link Menu',
            'type' => 'custom_link',
            'url' => '',
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertSessionHasErrors('url');
});

test('menu form validation requires related_id when type is route', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.website.menus.store'), [
            'name' => 'Route Menu',
            'type' => 'route',
            'related_id' => null,
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertSessionHasErrors('related_id');
});

test('menu form validation allows selecting a route and saves successfully', function () {
    $this->actingAs(User::factory()->admin()->create());

    $route = Route::factory()->create(['name' => 'Hà Nội - Hải Phòng']);

    $this->post(route('admin.website.menus.store'), [
        'name' => 'Route Menu Success',
        'type' => 'route',
        'related_id' => $route->id,
        'parent_id' => Menu::ROOT_PARENT_ID,
    ])->assertRedirect();

    $this->assertDatabaseHas('menus', [
        'name' => 'Route Menu Success',
        'type' => 'route',
        'related_id' => $route->id,
    ]);
});

test('menu form validation requires URL when type is page or system_page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.website.menus.store'), [
            'name' => 'Page Menu',
            'type' => 'page',
            'url' => '',
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertSessionHasErrors('url');

    $this->actingAs($admin)
        ->post(route('admin.website.menus.store'), [
            'name' => 'System Page Menu',
            'type' => 'system_page',
            'url' => '',
            'parent_id' => Menu::ROOT_PARENT_ID,
        ])
        ->assertSessionHasErrors('url');
});
