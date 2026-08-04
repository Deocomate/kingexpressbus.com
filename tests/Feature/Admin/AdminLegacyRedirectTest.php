<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permanently redirects common filament admin paths to quan-tri', function (string $from, string $to) {
    $this->get($from)
        ->assertRedirect($to)
        ->assertStatus(301);
})->with([
    'dashboard' => ['/admin', '/quan-tri'],
    'bookings' => ['/admin/bookings', '/quan-tri/dat-ve'],
    'trips' => ['/admin/trips', '/quan-tri/chuyen-xe'],
    'login' => ['/admin/login', '/quan-tri/dang-nhap'],
]);

it('permanently redirects unknown admin paths to quan-tri dashboard', function () {
    $this->get('/admin/something-weird-xyz')
        ->assertRedirect('/quan-tri')
        ->assertStatus(301);
});

it('rewrites create and record paths for common modules', function (string $from, string $to) {
    $this->get($from)
        ->assertRedirect($to)
        ->assertStatus(301);
})->with([
    'booking create' => ['/admin/bookings/create', '/quan-tri/dat-ve/tao'],
    'booking show' => ['/admin/bookings/42', '/quan-tri/dat-ve/42'],
    'booking edit' => ['/admin/bookings/42/edit', '/quan-tri/dat-ve/42'],
    'trip create' => ['/admin/trips/create', '/quan-tri/chuyen-xe/tao'],
]);
