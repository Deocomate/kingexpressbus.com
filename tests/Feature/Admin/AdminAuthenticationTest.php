<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    RateLimiter::clear('admin-auth');
});

// ─── Guest access ───────────────────────────────────────────────────────────

it('redirects guest to admin login when accessing protected route', function () {
    $this->get('/quan-tri')->assertRedirect(route('admin.login'));
});

it('shows login form to guest', function () {
    $this->get(route('admin.login'))->assertOk()->assertViewIs('admin.auth.login');
});

// ─── Role access ────────────────────────────────────────────────────────────

it('allows admin role to access dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
});

it('returns 403 for customer role', function () {
    $customer = User::factory()->customer()->create();

    $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
});

it('returns 403 for guest role', function () {
    $guest = User::factory()->create(['role' => 'guest']);

    $this->actingAs($guest)->get(route('admin.dashboard'))->assertForbidden();
});

// ─── Login logic ────────────────────────────────────────────────────────────

it('logs in admin with valid credentials and regenerates session', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $sessionBefore = session()->getId();

    $response = $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);

    // Session ID must change on login (regenerate)
    expect(session()->getId())->not->toBe($sessionBefore);
});

it('rejects wrong password and does not create session', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

    $response->assertRedirect(route('admin.login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('rejects customer email with correct password (no oracle: same error message)', function () {
    User::factory()->customer()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $responseBadPass = $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'customer@example.com',
            'password' => 'wrong-password',
        ]);

    // Capture error from first request's session before second request overwrites it
    $errorBadPass = $responseBadPass->getSession()->get('errors')?->get('email')[0] ?? null;

    $responseCorrectPass = $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'customer@example.com',
            'password' => 'secret123',
        ]);

    $errorCorrectPass = $responseCorrectPass->getSession()->get('errors')?->get('email')[0] ?? null;

    // Both must fail with a redirect and errors
    $responseBadPass->assertRedirect(route('admin.login'));
    $responseCorrectPass->assertRedirect(route('admin.login'));

    // Both error messages must be identical — no oracle
    expect($errorBadPass)->not->toBeNull();
    expect($errorCorrectPass)->not->toBeNull();
    expect($errorBadPass)->toBe($errorCorrectPass);

    $this->assertGuest();
});

it('does not authenticate customer even with valid credentials', function () {
    User::factory()->customer()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'customer@example.com',
            'password' => 'secret123',
        ]);

    $this->assertGuest();
});

// ─── Logout ─────────────────────────────────────────────────────────────────

it('logs out and invalidates session', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $sessionBefore = session()->getId();

    $response = $this->post(route('admin.logout'));

    $response->assertRedirect(route('admin.login'));
    $this->assertGuest();
    expect(session()->getId())->not->toBe($sessionBefore);
});

// ─── Password change invalidates old session (AuthenticateSession) ───────────

it('invalidates old session when password changes', function () {
    $password = 'original-pass';
    $admin = User::factory()->admin()->create([
        'password' => Hash::make($password),
    ]);

    // Log in via the form so the session stores the password hash (required for AuthenticateSession)
    $this->post(route('admin.login.submit'), [
        'email' => $admin->email,
        'password' => $password,
    ])->assertRedirect(route('admin.dashboard'));

    // Verify session is active; this also stores password_hash_web in the session
    $this->get(route('admin.dashboard'))->assertOk();

    // Change password in DB while holding the session cookie.
    // The 'hashed' cast auto-hashes plain strings, so pass plain text.
    $admin->update(['password' => 'new-pass']);

    // Reset auth guard cache so the next request fetches the user fresh from DB.
    // In production this is automatic (new process per request), but tests share state.
    app('auth')->forgetGuards();

    // AuthenticateSession detects the hash mismatch (session has old hash, DB has new hash)
    // and redirects to admin login.
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('admin.login'));
});

// ─── Rate limiter independence ───────────────────────────────────────────────

it('admin limiter does not trigger when client login is spammed', function () {
    // Clear both limiters
    RateLimiter::clear('auth');
    RateLimiter::clear('admin-auth');

    User::factory()->create([
        'email' => 'customer@example.com',
        'password' => Hash::make('password'),
        'role' => 'customer',
    ]);

    // Exhaust the client auth limiter (5 attempts)
    for ($i = 0; $i < 6; $i++) {
        $this->post(route('client.login.submit'), [
            'login' => 'customer@example.com',
            'password' => 'wrong',
        ]);
    }

    // Admin login must still work (429 NOT triggered)
    $admin = User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('admin-pass'),
    ]);

    $response = $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'admin-pass',
        ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);
});

it('admin login returns 429 after 5 failed attempts', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('secret'),
    ]);

    $payload = [
        'email' => 'admin@example.com',
        'password' => 'wrong',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('admin.login.submit'), $payload);
    }

    $response = $this->post(route('admin.login.submit'), $payload);
    $response->assertStatus(429);
});
