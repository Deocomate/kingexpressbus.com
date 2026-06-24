<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('logs in a customer with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('password123'),
        'role' => 'customer',
    ]);

    $response = $this->post(route('client.login.submit'), [
        'login' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('client.profile.index'));
    $response->assertSessionHas('success', __('client.auth.flash.login_success'));
    $this->assertAuthenticatedAs($user);
});

it('rejects login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('password123'),
        'role' => 'customer',
    ]);

    $response = $this->from(route('client.login'))->post(route('client.login.submit'), [
        'login' => 'customer@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('client.login'));
    $response->assertSessionHasErrors('login');
    $this->assertGuest();
});

it('returns the same password reset message for missing email', function () {
    User::factory()->create([
        'email' => 'existing@example.com',
        'password' => Hash::make('password123'),
        'role' => 'customer',
    ]);

    $expectedMessage = __('client.auth.password.reset_link_sent');

    $existingUserResponse = $this->from(route('client.password.request'))->post(route('client.password.email'), [
        'email' => 'existing@example.com',
    ]);

    $missingUserResponse = $this->from(route('client.password.request'))->post(route('client.password.email'), [
        'email' => 'missing@example.com',
    ]);

    $existingUserResponse->assertRedirect(route('client.password.request'));
    $existingUserResponse->assertSessionHas('status', $expectedMessage);

    $missingUserResponse->assertRedirect(route('client.password.request'));
    $missingUserResponse->assertSessionHas('status', $expectedMessage);
});
