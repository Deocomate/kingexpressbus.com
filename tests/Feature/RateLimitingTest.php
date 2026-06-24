<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    RateLimiter::clear('auth');
});

it('returns 429 on the sixth login attempt', function () {
    User::factory()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('password123'),
        'role' => 'customer',
    ]);

    $payload = [
        'login' => 'customer@example.com',
        'password' => 'wrong-password',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('client.login.submit'), $payload);
    }

    $response = $this->post(route('client.login.submit'), $payload);

    $response->assertStatus(429);
});
