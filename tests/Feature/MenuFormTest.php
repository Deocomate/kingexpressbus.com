<?php

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('menu form validation requires URL when type is custom_link', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateMenu::class)
        ->fillForm([
            'name' => 'Custom Link Menu',
            'type' => 'custom_link',
            'url' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['url' => 'required']);
});

test('menu form validation requires related_id when type is route', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateMenu::class)
        ->fillForm([
            'name' => 'Route Menu',
            'type' => 'route',
            'related_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['related_id' => 'required']);
});

test('menu form validation allows selecting a route and saves successfully', function () {
    $this->actingAs(User::factory()->admin()->create());

    $route = Route::factory()->create(['name' => 'Hà Nội - Hải Phòng']);

    Livewire::test(CreateMenu::class)
        ->fillForm([
            'name' => 'Route Menu Success',
            'type' => 'route',
            'related_id' => $route->id,
            'priority' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('menus', [
        'name' => 'Route Menu Success',
        'type' => 'route',
        'related_id' => $route->id,
        'priority' => 10,
    ]);
});

test('menu form validation requires URL when type is page or system_page', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateMenu::class)
        ->fillForm([
            'name' => 'Page Menu',
            'type' => 'page',
            'url' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['url' => 'required']);

    Livewire::test(CreateMenu::class)
        ->fillForm([
            'name' => 'System Page Menu',
            'type' => 'system_page',
            'url' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['url' => 'required']);
});
