<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('every quan-tri route outside the auth group has admin.auth middleware', function () {
    $routes = Route::getRoutes();

    $violations = [];

    foreach ($routes as $route) {
        $uri = $route->uri();

        if (! str_starts_with($uri, 'quan-tri')) {
            continue;
        }

        // Auth routes are explicitly excluded from the protected group
        $name = $route->getName() ?? '';
        if (in_array($name, ['admin.login', 'admin.login.submit', 'admin.logout'])) {
            continue;
        }

        $middleware = $route->middleware();

        if (! in_array('admin.auth', $middleware)) {
            $violations[] = sprintf('%s (%s): missing admin.auth', $uri, $name);
        }
    }

    expect($violations)
        ->toBeEmpty('Routes missing admin.auth: '.implode(', ', $violations));
});

it('admin.auth and admin.transaction are not applied to auth routes', function () {
    $routes = Route::getRoutes();

    foreach ($routes as $route) {
        $name = $route->getName() ?? '';

        if (! in_array($name, ['admin.login', 'admin.login.submit', 'admin.logout'])) {
            continue;
        }

        $middleware = $route->middleware();

        expect($middleware)
            ->not->toContain('admin.auth', "Auth route [{$name}] should not have admin.auth middleware")
            ->not->toContain('admin.transaction', "Auth route [{$name}] should not have admin.transaction middleware");
    }
});
