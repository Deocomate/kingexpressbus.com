<?php

use App\Mail\BookingApprovedMail;
use App\Mail\BookingConfirmMail;
use App\Mail\BookingPaymentRequestMail;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function seedSePayTripFixture(string $suffix = 'sepay'): array
{
    $now = now();

    $provinceStartId = DB::table('provinces')->insertGetId([
        'name' => 'Ha Noi '.$suffix,
        'slug' => 'ha-noi-'.$suffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $provinceEndId = DB::table('provinces')->insertGetId([
        'name' => 'Lao Cai '.$suffix,
        'slug' => 'lao-cai-'.$suffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtTypeId = DB::table('district_types')->insertGetId([
        'name' => 'Urban '.$suffix,
        'priority' => 0,
    ]);

    $districtStartId = DB::table('districts')->insertGetId([
        'province_id' => $provinceStartId,
        'district_type_id' => $districtTypeId,
        'name' => 'Thanh Xuan '.$suffix,
        'slug' => 'thanh-xuan-'.$suffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtEndId = DB::table('districts')->insertGetId([
        'province_id' => $provinceEndId,
        'district_type_id' => $districtTypeId,
        'name' => 'Sapa '.$suffix,
        'slug' => 'sapa-'.$suffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $pickupStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtStartId,
        'name' => 'Ben Xe My Dinh '.$suffix,
        'address' => 'Ha Noi',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $dropoffStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtEndId,
        'name' => 'Ben Xe Sapa '.$suffix,
        'address' => 'Lao Cai',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $routeId = DB::table('routes')->insertGetId([
        'province_start_id' => $provinceStartId,
        'province_end_id' => $provinceEndId,
        'name' => 'Ha Noi - Sapa '.$suffix,
        'slug' => 'ha-noi-sapa-'.$suffix,
        'price_default' => 0,
        'available_hotel_pickup' => false,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    DB::table('route_stops')->insert([
        [
            'route_id' => $routeId,
            'stop_id' => $pickupStopId,
            'stop_type' => 'pickup',
            'priority' => 10,
            'created_at' => $now,
            'updated_at' => $now,
        ],
        [
            'route_id' => $routeId,
            'stop_id' => $dropoffStopId,
            'stop_type' => 'dropoff',
            'priority' => 9,
            'created_at' => $now,
            'updated_at' => $now,
        ],
    ]);

    $busId = DB::table('buses')->insertGetId([
        'name' => 'King Sleeper '.$suffix,
        'model_name' => 'Limousine',
        'seat_count' => 40,
        'seat_map' => json_encode([]),
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $tripId = DB::table('trips')->insertGetId([
        'bus_id' => $busId,
        'route_id' => $routeId,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'price' => 300000,
        'is_active' => true,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return [
        'trip_id' => $tripId,
        'pickup_stop_id' => $pickupStopId,
        'dropoff_stop_id' => $dropoffStopId,
    ];
}

function sePayBookingRequestPayload(array $fixture, string $paymentMethod): array
{
    return [
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '02/02/2026',
        'quantity' => 1,
        'customer_name' => 'Test User',
        'customer_phone' => '0900000000',
        'customer_email' => 'test@example.com',
        'pickup_stop_id' => $fixture['pickup_stop_id'],
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'total_price' => 300000,
        'payment_method' => $paymentMethod,
    ];
}

function createSePayPendingBooking(string $suffix = 'ipn', array $overrides = []): object
{
    $fixture = seedSePayTripFixture($suffix);
    $now = now();
    $bookingData = [
        'booking_code' => 'SEPAY-'.strtoupper($suffix),
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '2026-02-02',
        'customer_name' => 'IPN User',
        'customer_phone' => '0900000000',
        'customer_email' => 'ipn@example.com',
        'pickup_stop_id' => $fixture['pickup_stop_id'],
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'quantity' => 1,
        'total_price' => 300000,
        'payment_method' => 'online_banking',
        'payment_status' => 'unpaid',
        'status' => 'pending',
        'confirmed_at' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    $bookingId = DB::table('bookings')->insertGetId(array_merge($bookingData, $overrides));

    return DB::table('bookings')->where('id', $bookingId)->first();
}

function sePayIpnPayload(string $bookingCode, int $amount = 300000, string $transactionId = 'TX-SEPAY-001'): array
{
    return [
        'timestamp' => 1757058220,
        'notification_type' => 'ORDER_PAID',
        'order' => [
            'order_invoice_number' => $bookingCode,
            'order_amount' => (string) $amount,
            'order_currency' => 'VND',
        ],
        'transaction' => [
            'transaction_id' => $transactionId,
            'transaction_amount' => (string) $amount,
            'transaction_currency' => 'VND',
            'transaction_date' => '2026-05-25 10:30:00',
            'transaction_status' => 'APPROVED',
        ],
    ];
}

it('keeps online banking bookings on the success flow and sends the confirmation email', function () {
    Mail::fake();

    $fixture = seedSePayTripFixture('online');
    $response = $this->post(route('client.booking.store'), sePayBookingRequestPayload($fixture, 'online_banking'));

    $booking = DB::table('bookings')->first();

    $response->assertRedirect(route('client.booking.success'));
    expect($booking->payment_status)->toBe('unpaid')
        ->and($booking->status)->toBe('pending');
    Mail::assertQueued(BookingConfirmMail::class, 2);
});

it('keeps cash bookings on the existing success flow and sends the confirmation email', function () {
    Mail::fake();

    $fixture = seedSePayTripFixture('cash');
    $response = $this->post(route('client.booking.store'), sePayBookingRequestPayload($fixture, 'cash_on_pickup'));

    $response->assertRedirect(route('client.booking.success'));
    Mail::assertQueued(BookingConfirmMail::class, 2);
});

it('sends a payment request email when admin confirms an online banking booking', function () {
    Mail::fake();

    $booking = createSePayPendingBooking('confirm-online');

    app(BookingService::class)->updateStatus($booking->id, 'confirmed');

    $updatedBooking = DB::table('bookings')->where('id', $booking->id)->first();

    expect($updatedBooking->status)->toBe('confirmed')
        ->and($updatedBooking->confirmed_at)->not->toBeNull();

    Mail::assertQueued(BookingPaymentRequestMail::class, 2);
    Mail::assertNotQueued(BookingApprovedMail::class);
});

it('sends approval email when admin confirms a cash booking', function () {
    Mail::fake();

    $booking = createSePayPendingBooking('confirm-cash', [
        'payment_method' => 'cash_on_pickup',
    ]);

    app(BookingService::class)->updateStatus($booking->id, 'confirmed');

    $updatedBooking = DB::table('bookings')->where('id', $booking->id)->first();

    expect($updatedBooking->status)->toBe('confirmed')
        ->and($updatedBooking->confirmed_at)->not->toBeNull();

    Mail::assertQueued(BookingApprovedMail::class, 2);
    Mail::assertNotQueued(BookingPaymentRequestMail::class);
});

it('does not reset confirmed time or resend payment request when status is not pending to confirmed', function () {
    Mail::fake();

    $confirmedAt = now()->subHours(2)->startOfSecond();
    $booking = createSePayPendingBooking('reconfirm', [
        'status' => 'confirmed',
        'confirmed_at' => $confirmedAt,
    ]);

    app(BookingService::class)->updateStatus($booking->id, 'confirmed');

    $updatedBooking = DB::table('bookings')->where('id', $booking->id)->first();

    expect(Carbon::parse($updatedBooking->confirmed_at)->eq($confirmedAt))->toBeTrue();
    Mail::assertNotQueued(BookingPaymentRequestMail::class);
});

it('blocks payment redirect for bookings that are not payable', function (array $overrides) {
    $booking = createSePayPendingBooking('redirect-'.strtolower($overrides['status'] ?? $overrides['payment_method']), $overrides);

    $response = $this->get(route('client.sepay.redirect', ['code' => $booking->booking_code]));

    $response->assertRedirect(route('client.home'));
    $response->assertSessionHas('error');
})->with([
    'cancelled booking' => [[
        'status' => 'cancelled',
        'confirmed_at' => null,
    ]],
    'pending booking' => [[
        'status' => 'pending',
        'confirmed_at' => null,
    ]],
    'cash booking' => [[
        'status' => 'confirmed',
        'confirmed_at' => now(),
        'payment_method' => 'cash_on_pickup',
    ]],
]);

it('redirects already paid bookings away from the payment page', function () {
    $booking = createSePayPendingBooking('redirect-paid', [
        'status' => 'confirmed',
        'confirmed_at' => now(),
        'payment_status' => 'paid',
    ]);

    $response = $this->get(route('client.sepay.redirect', ['code' => $booking->booking_code]));

    $response->assertRedirect(route('client.booking.success'));
});

it('returns the latest payment status for the success page poller', function () {
    $booking = createSePayPendingBooking('poll-paid', [
        'status' => 'confirmed',
        'confirmed_at' => now(),
        'payment_status' => 'paid',
    ]);

    $this->getJson(route('client.booking.payment_status', ['code' => $booking->booking_code]))
        ->assertSuccessful()
        ->assertJson([
            'booking_code' => $booking->booking_code,
            'status' => 'confirmed',
            'payment_method' => 'online_banking',
            'payment_status' => 'paid',
        ]);
});

it('rejects SePay IPN requests with missing or invalid secrets', function (array $headers) {
    config(['sepay.secret_key' => 'test-secret']);

    $booking = createSePayPendingBooking('bad-secret');

    $response = $this->postJson(route('client.sepay.ipn'), sePayIpnPayload($booking->booking_code), $headers);

    $response->assertUnauthorized();
})->with([
    'missing secret' => [[]],
    'invalid secret' => [['X-Secret-Key' => 'wrong-secret']],
]);

it('marks bookings paid from a valid SePay IPN and queues approval email', function () {
    Mail::fake();
    config(['sepay.secret_key' => 'test-secret']);

    $booking = createSePayPendingBooking('valid', [
        'status' => 'confirmed',
        'confirmed_at' => now(),
    ]);
    $response = $this->postJson(
        route('client.sepay.ipn'),
        sePayIpnPayload($booking->booking_code, 300000, 'TX-VALID-001'),
        ['X-Secret-Key' => 'test-secret']
    );

    $response->assertSuccessful();

    $updatedBooking = DB::table('bookings')->where('id', $booking->id)->first();
    $paymentLog = json_decode($updatedBooking->payment_log, true);

    expect($updatedBooking->payment_status)->toBe('paid')
        ->and($updatedBooking->status)->toBe('confirmed')
        ->and($updatedBooking->payment_transaction_id)->toBe('TX-VALID-001')
        ->and($paymentLog['transaction']['transaction_id'])->toBe('TX-VALID-001');

    Mail::assertQueued(BookingApprovedMail::class, 2);
});

it('does not send duplicate approval emails for repeated SePay IPN payloads', function () {
    Mail::fake();
    config(['sepay.secret_key' => 'test-secret']);

    $booking = createSePayPendingBooking('repeat', [
        'status' => 'confirmed',
        'confirmed_at' => now(),
    ]);
    $payload = sePayIpnPayload($booking->booking_code, 300000, 'TX-REPEAT-001');

    $this->postJson(route('client.sepay.ipn'), $payload, ['X-Secret-Key' => 'test-secret'])->assertSuccessful();
    $this->postJson(route('client.sepay.ipn'), $payload, ['X-Secret-Key' => 'test-secret'])->assertSuccessful();

    Mail::assertQueued(BookingApprovedMail::class, 2);
});

it('does not mark a booking paid when the SePay amount mismatches', function () {
    Mail::fake();
    config(['sepay.secret_key' => 'test-secret']);

    $booking = createSePayPendingBooking('amount', [
        'status' => 'confirmed',
        'confirmed_at' => now(),
    ]);
    $response = $this->postJson(
        route('client.sepay.ipn'),
        sePayIpnPayload($booking->booking_code, 299000, 'TX-WRONG-AMOUNT'),
        ['X-Secret-Key' => 'test-secret']
    );

    $response->assertSuccessful();

    $updatedBooking = DB::table('bookings')->where('id', $booking->id)->first();

    expect($updatedBooking->payment_status)->toBe('unpaid')
        ->and($updatedBooking->status)->toBe('confirmed')
        ->and($updatedBooking->payment_transaction_id)->toBeNull();

    Mail::assertNotQueued(BookingApprovedMail::class);
});

it('does not mark unconfirmed or cancelled bookings paid from SePay IPN', function (string $suffix, array $overrides) {
    Mail::fake();
    config(['sepay.secret_key' => 'test-secret']);

    $booking = createSePayPendingBooking($suffix, $overrides);

    $this->postJson(
        route('client.sepay.ipn'),
        sePayIpnPayload($booking->booking_code, 300000, 'TX-'.strtoupper($suffix)),
        ['X-Secret-Key' => 'test-secret']
    )->assertSuccessful();

    $updatedBooking = DB::table('bookings')->where('id', $booking->id)->first();

    expect($updatedBooking->payment_status)->toBe('unpaid')
        ->and($updatedBooking->status)->toBe($overrides['status']);

    Mail::assertNotQueued(BookingApprovedMail::class);
})->with([
    'pending booking' => ['ipn-pending', [
        'status' => 'pending',
        'confirmed_at' => null,
    ]],
    'cancelled booking' => ['ipn-cancelled', [
        'status' => 'cancelled',
        'confirmed_at' => now()->subHours(4),
    ]],
]);
