<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

// Shared admin user is created per-test via beforeEach so we don't clash with
// global function declarations in other test files loaded in the same process.
beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

// ─── Guest / role protection ─────────────────────────────────────────────────

it('redirects unauthenticated user from bookings index', function () {
    $this->get(route('admin.bookings.index'))->assertRedirect(route('admin.login'));
});

it('returns 403 for customer role on bookings index', function () {
    $customer = User::factory()->customer()->create();
    $this->actingAs($customer)->get(route('admin.bookings.index'))->assertForbidden();
});

it('allows admin to view bookings index', function () {
    $this->actingAs($this->admin)->get(route('admin.bookings.index'))->assertOk();
});

// ─── 5-tab filtering ─────────────────────────────────────────────────────────

it('upcoming tab shows only future pending and confirmed bookings', function () {
    $futureDate = now()->addDay()->toDateString();
    $pastDate   = now()->subDay()->toDateString();

    $upcomingPending   = Booking::factory()->create(['status' => 'pending',   'booking_date' => $futureDate]);
    $upcomingConfirmed = Booking::factory()->create(['status' => 'confirmed', 'booking_date' => $futureDate]);
    $upcomingCompleted = Booking::factory()->create(['status' => 'completed', 'booking_date' => $futureDate]);
    $pastPending       = Booking::factory()->create(['status' => 'pending',   'booking_date' => $pastDate]);
    $cancelled         = Booking::factory()->create(['status' => 'cancelled', 'booking_date' => $futureDate]);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'upcoming']));
    $response->assertOk();

    $codes = $response->viewData('paginator')->pluck('booking_code');
    expect($codes)->toContain($upcomingPending->booking_code)
        ->and($codes)->toContain($upcomingConfirmed->booking_code)
        ->and($codes)->not->toContain($upcomingCompleted->booking_code)
        ->and($codes)->not->toContain($pastPending->booking_code)
        ->and($codes)->not->toContain($cancelled->booking_code);
});

it('pending tab shows only status=pending bookings', function () {
    $pending   = Booking::factory()->create(['status' => 'pending']);
    $confirmed = Booking::factory()->create(['status' => 'confirmed']);
    $cancelled = Booking::factory()->create(['status' => 'cancelled']);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'pending']));
    $codes = $response->viewData('paginator')->pluck('booking_code');

    expect($codes)->toContain($pending->booking_code)
        ->and($codes)->not->toContain($confirmed->booking_code)
        ->and($codes)->not->toContain($cancelled->booking_code);
});

it('completed tab shows only status=completed bookings', function () {
    $completed = Booking::factory()->create(['status' => 'completed']);
    $pending   = Booking::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'completed']));
    $codes = $response->viewData('paginator')->pluck('booking_code');

    expect($codes)->toContain($completed->booking_code)
        ->and($codes)->not->toContain($pending->booking_code);
});

it('cancelled tab shows only status=cancelled bookings', function () {
    $cancelled = Booking::factory()->create(['status' => 'cancelled']);
    $pending   = Booking::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'cancelled']));
    $codes = $response->viewData('paginator')->pluck('booking_code');

    expect($codes)->toContain($cancelled->booking_code)
        ->and($codes)->not->toContain($pending->booking_code);
});

it('all tab shows every booking regardless of status', function () {
    $pending   = Booking::factory()->create(['status' => 'pending']);
    $cancelled = Booking::factory()->create(['status' => 'cancelled']);
    $completed = Booking::factory()->create(['status' => 'completed']);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'all']));
    $codes    = $response->viewData('paginator')->pluck('booking_code');

    expect($codes)->toContain($pending->booking_code)
        ->and($codes)->toContain($cancelled->booking_code)
        ->and($codes)->toContain($completed->booking_code);
});

it('default tab is upcoming', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index'));
    expect($response->viewData('activeTab'))->toBe('upcoming');
});

// ─── Badge counts ─────────────────────────────────────────────────────────────

it('upcoming badge equals correct count', function () {
    $future = now()->addDay()->toDateString();

    Booking::factory()->create(['status' => 'pending',   'booking_date' => $future]);
    Booking::factory()->create(['status' => 'confirmed', 'booking_date' => $future]);
    Booking::factory()->create(['status' => 'pending',   'booking_date' => now()->subDay()->toDateString()]);
    Booking::factory()->create(['status' => 'cancelled', 'booking_date' => $future]);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index'));
    $badges   = $response->viewData('badges');

    expect($badges['upcoming'])->toBe(2);
});

it('pending badge equals total pending count', function () {
    Booking::factory()->create(['status' => 'pending']);
    Booking::factory()->create(['status' => 'pending']);
    Booking::factory()->create(['status' => 'confirmed']);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index'));
    $badges   = $response->viewData('badges');

    expect($badges['pending'])->toBe(2);
});

// ─── Search ──────────────────────────────────────────────────────────────────

it('searches by customer phone number', function () {
    $target = Booking::factory()->create([
        'customer_phone' => '0909123456',
        'status'         => 'pending',
        'booking_date'   => now()->addDay()->toDateString(),
    ]);
    $other = Booking::factory()->create([
        'customer_phone' => '0888000000',
        'status'         => 'pending',
        'booking_date'   => now()->addDay()->toDateString(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.index', ['tab' => 'all', 'search' => '0909123456']));
    $codes = $response->viewData('paginator')->pluck('booking_code');

    expect($codes)->toContain($target->booking_code)
        ->and($codes)->not->toContain($other->booking_code);
});

it('searches by booking code', function () {
    $target = Booking::factory()->create(['booking_code' => 'KEB9999XX']);
    $other  = Booking::factory()->create(['booking_code' => 'KEB0000ZZ']);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.index', ['tab' => 'all', 'search' => 'KEB9999']));
    $codes = $response->viewData('paginator')->pluck('booking_code');

    expect($codes)->toContain('KEB9999XX')
        ->and($codes)->not->toContain('KEB0000ZZ');
});

// ─── Sort ──────────────────────────────────────────────────────────────────

it('sort by departure_at asc does not throw SQL error', function () {
    Booking::factory()->create(['status' => 'pending']);

    $this->actingAs($this->admin)
        ->get(route('admin.bookings.index', ['tab' => 'all', 'sort' => 'departure_at', 'direction' => 'asc']))
        ->assertOk();
});

it('sort by departure_at desc does not throw SQL error', function () {
    Booking::factory()->create(['status' => 'pending']);

    $this->actingAs($this->admin)
        ->get(route('admin.bookings.index', ['tab' => 'all', 'sort' => 'departure_at', 'direction' => 'desc']))
        ->assertOk();
});

// ─── Actions: confirm ─────────────────────────────────────────────────────────

it('confirm on a pending booking changes status to confirmed', function () {
    $booking = Booking::factory()->create(['status' => 'pending']);

    $this->actingAs($this->admin)
        ->post(route('admin.bookings.confirm', $booking))
        ->assertRedirect();

    expect($booking->fresh()->status->value)->toBe('confirmed');
});

it('confirm on a completed booking is rejected with error flash', function () {
    $booking = Booking::factory()->create(['status' => 'completed']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.confirm', $booking));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect($booking->fresh()->status->value)->toBe('completed');
});

// ─── Actions: complete ────────────────────────────────────────────────────────

it('complete on a confirmed booking changes status to completed', function () {
    $booking = Booking::factory()->create(['status' => 'confirmed']);

    $this->actingAs($this->admin)
        ->post(route('admin.bookings.complete', $booking))
        ->assertRedirect();

    expect($booking->fresh()->status->value)->toBe('completed');
});

it('complete on a pending booking is rejected', function () {
    $booking = Booking::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.complete', $booking));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect($booking->fresh()->status->value)->toBe('pending');
});

// ─── Actions: cancel ──────────────────────────────────────────────────────────

it('cancel with preset reason changes status to cancelled', function () {
    Mail::fake();
    $booking = Booking::factory()->create(['status' => 'pending', 'customer_email' => 'test@example.com']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.cancel', $booking), [
            'cancel_reason' => 'Hết chỗ trống cho chuyến xe này',
        ]);

    $response->assertRedirect();
    expect($booking->fresh()->status->value)->toBe('cancelled');
});

it('cancel with custom reason requires custom_reason field', function () {
    $booking = Booking::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.cancel', $booking), [
            'cancel_reason' => 'custom',
            'custom_reason' => '',
        ]);

    $response->assertSessionHasErrors('custom_reason');
    expect($booking->fresh()->status->value)->toBe('pending');
});

it('cancel queues cancellation email', function () {
    Mail::fake();
    $booking = Booking::factory()->create([
        'status'         => 'pending',
        'customer_email' => 'customer@example.com',
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.bookings.cancel', $booking), [
            'cancel_reason' => 'Hết chỗ trống cho chuyến xe này',
        ]);

    Mail::assertQueued(\App\Mail\BookingCancelledMail::class);
});

it('cancel on already-cancelled booking is rejected', function () {
    $booking = Booking::factory()->create(['status' => 'cancelled']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.cancel', $booking), [
            'cancel_reason' => 'Hết chỗ trống cho chuyến xe này',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('cancel on completed booking is rejected', function () {
    $booking = Booking::factory()->create(['status' => 'completed']);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.bookings.cancel', $booking), [
            'cancel_reason' => 'Hết chỗ trống cho chuyến xe này',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

// ─── store/update: status must not be written ─────────────────────────────────

it('store ignores status field in payload — status never reaches DB', function () {
    $trip    = Trip::factory()->create();
    $dropoff = Stop::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('admin.bookings.store'), [
            'booking_code'           => 'TESTCREATE01',
            'trip_id'                => $trip->id,
            'booking_date'           => now()->addDay()->toDateString(),
            'customer_name'          => 'Test User',
            'customer_phone'         => '0900000001',
            'dropoff_stop_id'        => $dropoff->id,
            'quantity'               => 1,
            'total_price'            => 100000,
            'payment_method'         => 'cash_on_pickup',
            'payment_status'         => 'unpaid',
            'base_unit_price'        => 100000,
            'global_surcharge_unit'  => 0,
            'route_surcharge_unit'   => 0,
            'final_unit_price'       => 100000,
            'total_surcharge_amount' => 0,
            'status'                 => 'confirmed',  // injected
            'confirmed_at'           => now()->toISOString(),
        ]);

    $booking = Booking::where('booking_code', 'TESTCREATE01')->first();
    expect($booking)->not->toBeNull()
        ->and($booking->status->value)->toBe('pending');
});

it('update ignores status field in payload — status unchanged in DB', function () {
    $booking = Booking::factory()->create(['status' => 'pending']);

    $this->actingAs($this->admin)
        ->put(route('admin.bookings.update', $booking), [
            'booking_code'           => $booking->booking_code,
            'trip_id'                => $booking->trip_id,
            'booking_date'           => $booking->booking_date->toDateString(),
            'customer_name'          => $booking->customer_name,
            'customer_phone'         => $booking->customer_phone,
            'dropoff_stop_id'        => $booking->dropoff_stop_id,
            'quantity'               => 1,
            'total_price'            => $booking->total_price,
            'payment_method'         => 'cash_on_pickup',
            'payment_status'         => 'unpaid',
            'base_unit_price'        => $booking->base_unit_price ?? 0,
            'global_surcharge_unit'  => 0,
            'route_surcharge_unit'   => 0,
            'final_unit_price'       => $booking->final_unit_price ?? 0,
            'total_surcharge_amount' => 0,
            'status'                 => 'confirmed',  // attacker injection
        ]);

    expect($booking->fresh()->status->value)->toBe('pending');
});

// ─── Partial poll endpoint ────────────────────────────────────────────────────

it('returns table partial from dedicated poll route', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.bookings.table'));

    $response->assertOk()
        ->assertViewIs('admin.bookings._table');
});

// ─── Filters ──────────────────────────────────────────────────────────────────

it('filters by status', function () {
    $pending = Booking::factory()->create(['status' => 'pending']);
    $confirmed = Booking::factory()->create(['status' => 'confirmed']);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', [
        'tab' => 'all',
        'filter' => ['status' => 'pending'],
    ]));

    $codes = $response->viewData('paginator')->pluck('booking_code');
    expect($codes)->toContain($pending->booking_code)
        ->and($codes)->not->toContain($confirmed->booking_code);
});

it('filters by payment_status', function () {
    $unpaid = Booking::factory()->create(['payment_status' => 'unpaid', 'status' => 'pending']);
    $paid = Booking::factory()->create(['payment_status' => 'paid', 'status' => 'pending']);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', [
        'tab' => 'all',
        'filter' => ['payment_status' => 'paid'],
    ]));

    $codes = $response->viewData('paginator')->pluck('booking_code');
    expect($codes)->toContain($paid->booking_code)
        ->and($codes)->not->toContain($unpaid->booking_code);
});

it('filters by booking_date range', function () {
    $inRange = Booking::factory()->create([
        'booking_date' => '2026-08-10',
        'status' => 'pending',
    ]);
    $outOfRange = Booking::factory()->create([
        'booking_date' => '2026-09-01',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', [
        'tab' => 'all',
        'filter' => ['from' => '2026-08-01', 'until' => '2026-08-31'],
    ]));

    $codes = $response->viewData('paginator')->pluck('booking_code');
    expect($codes)->toContain($inRange->booking_code)
        ->and($codes)->not->toContain($outOfRange->booking_code);
});

// ─── N+1 guard ────────────────────────────────────────────────────────────────

it('booking index stays under a fixed query budget', function () {
    Booking::factory()->count(8)->create([
        'status' => 'pending',
        'booking_date' => now()->addDay()->toDateString(),
    ]);

    $queryCount = 0;
    \Illuminate\Support\Facades\DB::listen(function () use (&$queryCount) {
        $queryCount++;
    });

    $this->actingAs($this->admin)->get(route('admin.bookings.index', ['tab' => 'all']))->assertOk();

    // Eager-load trip.route / stops / user + tab badges + auth — budget is fixed, not O(n rows).
    expect($queryCount)->toBeLessThanOrEqual(25);
});

it('store rejects unknown fields outside whitelist', function () {
    $trip = Trip::factory()->create();
    $dropoff = Stop::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('admin.bookings.store'), [
            'booking_code'           => 'TESTWHITELIST1',
            'trip_id'                => $trip->id,
            'booking_date'           => now()->addDay()->toDateString(),
            'customer_name'          => 'Test User',
            'customer_phone'         => '0900000002',
            'dropoff_stop_id'        => $dropoff->id,
            'quantity'               => 1,
            'total_price'            => 100000,
            'payment_method'         => 'cash_on_pickup',
            'base_unit_price'        => 100000,
            'global_surcharge_unit'  => 0,
            'route_surcharge_unit'   => 0,
            'final_unit_price'       => 100000,
            'total_surcharge_amount' => 0,
            'not_a_real_field'       => 'should-be-ignored',
        ])
        ->assertRedirect();

    $booking = Booking::where('booking_code', 'TESTWHITELIST1')->first();
    expect($booking)->not->toBeNull();
    expect($booking->getAttributes())->not->toHaveKey('not_a_real_field');
});

it('show returns booking detail partial', function () {
    $booking = Booking::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('admin.bookings.show', $booking))
        ->assertOk()
        ->assertViewIs('admin.bookings._detail')
        ->assertSee($booking->booking_code);
});
