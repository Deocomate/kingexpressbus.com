<?php

namespace App\Services;

use App\Helpers\SystemHelper;
use App\Mail\BookingApprovedMail;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmMail;
use App\Mail\BookingPaymentRequestMail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Service class for handling booking-related business logic.
 */
class BookingService
{
    private const NOTE_HOTEL_PICKUP_PREFIX = '[HOTEL_PICKUP]: ';
    private const NOTE_CUSTOMER_PREFIX = '[CUSTOMER_NOTE]: ';
    private const NOTE_CANCEL_PREFIX = '[CANCEL_REASON]: ';
    private const NOTE_ADMIN_CANCEL_PREFIX = '[ADMIN_CANCEL_REASON]: ';
    private const NOTE_SEPAY_REFUND_PREFIX = '[SEPAY_REFUND_REQUIRED]: ';

    private const LEGACY_NOTE_HOTEL_PICKUP_PREFIX = '[Đón tại khách sạn]: ';
    private const LEGACY_NOTE_ADMIN_CANCEL_PREFIX = '[Lý do hủy Admin]: ';

    protected TripService $tripService;

    public function __construct(TripService $tripService)
    {
        $this->tripService = $tripService;
    }

    /**
     * Create a new booking with transaction safety.
     */
    public function createBooking(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Get trip details to verify availability
            $trip = DB::table('trips as t')
                ->join('buses as b', 't.bus_id', '=', 'b.id')
                ->where('t.id', $data['trip_id'])
                ->select('b.seat_count', 't.is_active')
                ->first();

            if (!$trip || !$trip->is_active) {
                return [
                    'success' => false,
                    'message' => __('client.booking.create.trip_not_found'),
                ];
            }

            // Calculate available seats with lock
            $bookedSeats = DB::table('bookings')
                ->where('trip_id', $data['trip_id'])
                ->whereDate('booking_date', $data['booking_date'])
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->lockForUpdate()
                ->sum('quantity');

            $availableSeats = $trip->seat_count - $bookedSeats;
            $requestedQuantity = (int) $data['quantity'];

            if ($requestedQuantity > $availableSeats) {
                return [
                    'success' => false,
                    'message' => __('client.booking.store.not_enough_seats', [
                        'requested' => $requestedQuantity,
                        'available' => $availableSeats,
                    ]),
                ];
            }

            $baseUnitPrice = (int) ($data['base_unit_price'] ?? 0);
            $globalSurchargeUnit = (int) ($data['global_surcharge_unit'] ?? 0);
            $routeSurchargeUnit = (int) ($data['route_surcharge_unit'] ?? 0);
            $finalUnitPrice = (int) ($data['final_unit_price'] ?? 0);
            $totalSurchargeAmount = (int) ($data['total_surcharge_amount'] ?? 0);
            $surchargeReasonSnapshot = $data['surcharge_reason_snapshot'] ?? null;

            // Process hotel pickup if applicable
            $pickupStopId = $data['pickup_stop_id'] ?? null;
            $bookingNotes = isset($data['notes']) ? trim(strip_tags($data['notes'])) : null;
            $bookingNotes = $bookingNotes !== '' ? $bookingNotes : null;

            if (isset($data['is_hotel_pickup']) && $data['is_hotel_pickup']) {
                $hotelAddress = trim(strip_tags($data['hotel_pickup_address'] ?? ''));
                $hotelNote = self::NOTE_HOTEL_PICKUP_PREFIX . $hotelAddress;
                $bookingNotes = $bookingNotes
                    ? $hotelNote . "\n" . self::NOTE_CUSTOMER_PREFIX . $bookingNotes
                    : $hotelNote;
                $pickupStopId = null;
            }

            $bookingCode = SystemHelper::generateBookingCode();

            // Create the booking
            $bookingId = DB::table('bookings')->insertGetId([
                'user_id' => $data['user_id'] ?? null,
                'trip_id' => $data['trip_id'],
                'booking_date' => $data['booking_date'],
                'booking_code' => $bookingCode,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'],
                'pickup_stop_id' => $pickupStopId,
                'dropoff_stop_id' => $data['dropoff_stop_id'],
                'quantity' => $requestedQuantity,
                'total_price' => $data['total_price'],
                'base_unit_price' => $baseUnitPrice,
                'global_surcharge_unit' => $globalSurchargeUnit,
                'route_surcharge_unit' => $routeSurchargeUnit,
                'final_unit_price' => $finalUnitPrice,
                'total_surcharge_amount' => $totalSurchargeAmount,
                'surcharge_reason_snapshot' => $surchargeReasonSnapshot,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'notes' => $bookingNotes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'success' => true,
                'booking_id' => $bookingId,
                'booking_code' => $bookingCode,
                'message' => __('client.booking.service.create_success'),
            ];
        });
    }

    /**
     * Send booking confirmation email.
     */
    public function sendConfirmationEmail(int $bookingId): bool
    {
        try {
            $mailDetails = $this->prepareMailDetails($bookingId);

            if (!$mailDetails) {
                Log::error('Cannot prepare booking confirmation mail data: ' . $bookingId);
                return false;
            }

            // Send to customer
            Mail::to($mailDetails['customer_email'])->queue(new BookingConfirmMail($mailDetails));

            // Send to admin
            $adminEmail = config('mail.admin_email', 'kingexpressbus@gmail.com');
            Mail::to($adminEmail)->queue(new BookingConfirmMail($mailDetails));

            return true;
        } catch (\Throwable $e) {
            Log::error('Error while sending booking confirmation email', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Prepare mail details for booking confirmation.
     */
    public function prepareMailDetails(int $bookingId): ?array
    {
        $details = DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->join('buses as bus', 't.bus_id', '=', 'bus.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->leftJoin('stops as p_stop', 'b.pickup_stop_id', '=', 'p_stop.id')
            ->join('stops as d_stop', 'b.dropoff_stop_id', '=', 'd_stop.id')
            ->join('provinces as start_prov', 'r.province_start_id', '=', 'start_prov.id')
            ->join('provinces as end_prov', 'r.province_end_id', '=', 'end_prov.id')
            ->select([
                'b.*',
                'r.name as route_name',
                't.start_time',
                'bus.name as bus_name',
                'bus.model_name as bus_model_name',
                'p_stop.name as pickup_name',
                'p_stop.address as pickup_address',
                'd_stop.name as dropoff_name',
                'd_stop.address as dropoff_address',
                'start_prov.name as start_province',
                'end_prov.name as end_province',
            ])
            ->where('b.id', $bookingId)
            ->first();

        if (!$details) {
            return null;
        }

        $result = (array) $details;
        $webProfile = DB::table('web_profiles')->where('is_default', true)->first();

        $result['web_title'] = $webProfile->title ?? config('app.name');
        $result['web_phone'] = $webProfile->hotline ?? $webProfile->phone ?? __('client.booking.service.not_available');
        $result['web_email'] = $webProfile->email ?? __('client.booking.service.not_available');
        $result['web_link'] = url('/');
        $result['web_logo'] = !empty($webProfile->logo_url) ? url($webProfile->logo_url) : null;
        $result['payment_url'] = route('client.sepay.redirect', ['code' => $result['booking_code']]);

        $result['departure_date'] = Carbon::parse($result['booking_date'])->format('d/m/Y');
        $result['start_time'] = Carbon::parse($result['start_time'])->format('H:i');
        $result['bus_type_name'] = $result['bus_model_name'] ?? __('client.booking.common.updating');

        // Handle hotel pickup display
        $hotelAddress = $this->extractHotelPickupAddress($result['notes'] ?? null);
        if (is_null($result['pickup_stop_id']) && $hotelAddress !== null) {
            $result['pickup_info'] = __('client.booking.service.hotel_pickup_display', ['address' => $hotelAddress]);
        } else {
            $result['pickup_info'] = sprintf(
                '%s - %s',
                $result['pickup_name'] ?? __('client.booking.service.not_available'),
                $result['pickup_address'] ?? __('client.booking.service.not_available')
            );
        }

        $result['needs_bank_transfer_info'] = false;

        return $result;
    }

    /**
     * Send payment request email to customer after admin confirmation.
     */
    public function sendPaymentRequestEmail(int $bookingId): bool
    {
        try {
            $mailDetails = $this->prepareMailDetails($bookingId);

            if (!$mailDetails) {
                Log::error('Cannot prepare booking payment request mail data: ' . $bookingId);
                return false;
            }

            Mail::to($mailDetails['customer_email'])->queue(new BookingPaymentRequestMail($mailDetails));

            $adminEmail = config('mail.admin_email', 'kingexpressbus@gmail.com');
            Mail::to($adminEmail)->queue(new BookingPaymentRequestMail($mailDetails));

            Log::info('Booking payment request emails sent successfully', [
                'booking_id' => $bookingId,
                'booking_code' => $mailDetails['booking_code'] ?? __('client.booking.service.not_available'),
                'customer_email' => $mailDetails['customer_email'] ?? __('client.booking.service.not_available'),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Error while sending booking payment request emails', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(int $bookingId, ?string $reason = null, ?int $adminUserId = null): array
    {
        $booking = DB::table('bookings')->where('id', $bookingId)->first();

        if (!$booking) {
            return ['success' => false, 'message' => __('client.booking.service.cancel_not_found')];
        }

        if ($booking->status === 'cancelled') {
            return ['success' => false, 'message' => __('client.booking.service.cancel_already_cancelled')];
        }

        $updateData = [
            'status' => 'cancelled',
            'updated_at' => now(),
        ];

        if ($reason) {
            $existingNotes = $booking->notes ?? '';
            $cancelNote = $adminUserId ? self::NOTE_ADMIN_CANCEL_PREFIX : self::NOTE_CANCEL_PREFIX;
            $updateData['notes'] = $existingNotes . "\n" . $cancelNote . $reason;
        }

        DB::table('bookings')->where('id', $bookingId)->update($updateData);

        // Auto send cancellation email to customer
        $this->sendCancellationEmail($bookingId, $reason);

        return ['success' => true, 'message' => __('client.booking.service.cancel_success')];
    }

    /**
     * Send booking cancellation email to customer.
     */
    public function sendCancellationEmail(int $bookingId, ?string $reason = null): bool
    {
        try {
            $mailDetails = $this->prepareMailDetails($bookingId);

            if (!$mailDetails) {
                Log::error('Cannot prepare booking cancellation mail data: ' . $bookingId);
                return false;
            }

            $mailDetails['cancel_reason'] = $reason ?: __('client.booking.service.cancel_reason_default');

            // Send cancellation email to customer
            Mail::to($mailDetails['customer_email'])->queue(new BookingCancelledMail($mailDetails));

            // Send cancellation email to admin
            $adminEmail = config('mail.admin_email', 'kingexpressbus@gmail.com');
            Mail::to($adminEmail)->queue(new BookingCancelledMail($mailDetails));

            Log::info('Booking cancellation emails sent successfully', [
                'booking_id' => $bookingId,
                'booking_code' => $mailDetails['booking_code'] ?? __('client.booking.service.not_available'),
                'customer_email' => $mailDetails['customer_email'] ?? __('client.booking.service.not_available'),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Error while sending booking cancellation emails', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send booking approval email to customer.
     */
    public function sendApprovalEmail(int $bookingId): bool
    {
        try {
            $mailDetails = $this->prepareMailDetails($bookingId);

            if (!$mailDetails) {
                Log::error('Cannot prepare booking approval mail data: ' . $bookingId);
                return false;
            }

            Mail::to($mailDetails['customer_email'])->queue(new BookingApprovedMail($mailDetails));

            $adminEmail = config('mail.admin_email', 'kingexpressbus@gmail.com');
            Mail::to($adminEmail)->queue(new BookingApprovedMail($mailDetails));

            Log::info('Booking approval emails sent successfully', [
                'booking_id' => $bookingId,
                'booking_code' => $mailDetails['booking_code'] ?? __('client.booking.service.not_available'),
                'customer_email' => $mailDetails['customer_email'] ?? __('client.booking.service.not_available'),
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Error while sending booking approval emails', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Update booking status.
     */
    public function updateStatus(int $bookingId, string $status, ?string $notes = null): array
    {
        $booking = DB::table('bookings')->where('id', $bookingId)->first();

        if (!$booking) {
            return ['success' => false, 'message' => __('client.booking.service.status_not_found')];
        }

        $updateData = [
            'status' => $status,
            'updated_at' => now(),
        ];

        $isPendingToConfirmed = $status === 'confirmed' && $booking->status === 'pending';

        if ($isPendingToConfirmed) {
            $updateData['confirmed_at'] = now();
        }

        // Handle cancellation notes
        if ($status === 'cancelled') {
            $existingNotes = $booking->notes ?? '';
            $notesToAppend = [];

            if ($notes && !Str::contains($existingNotes, [self::NOTE_ADMIN_CANCEL_PREFIX, self::LEGACY_NOTE_ADMIN_CANCEL_PREFIX])) {
                $notesToAppend[] = self::NOTE_ADMIN_CANCEL_PREFIX . trim($notes);
            }

            if (
                $booking->payment_method === 'online_banking'
                && $booking->payment_status === 'paid'
                && !empty($booking->payment_transaction_id)
                && !Str::contains($existingNotes, self::NOTE_SEPAY_REFUND_PREFIX)
            ) {
                $notesToAppend[] = self::NOTE_SEPAY_REFUND_PREFIX
                    . 'Manual refund/reconciliation required for SePay transaction '
                    . $booking->payment_transaction_id
                    . '.';
            }

            if (!empty($notesToAppend)) {
                $updateData['notes'] = trim($existingNotes . "\n" . implode("\n", $notesToAppend));
            }
        }

        DB::table('bookings')->where('id', $bookingId)->update($updateData);

        // Auto send the correct email when moving from pending to confirmed
        if ($isPendingToConfirmed) {
            if ($booking->payment_method === 'online_banking') {
                $this->sendPaymentRequestEmail($bookingId);
            } else {
                $this->sendApprovalEmail($bookingId);
            }
        }

        // Auto send cancellation email when status is cancelled
        if ($status === 'cancelled') {
            $this->sendCancellationEmail($bookingId, $notes);
        }

        return ['success' => true, 'message' => __('client.booking.service.status_update_success')];
    }

    /**
     * Get booking details with full information.
     */
    public function getBookingDetails(int $bookingId): ?object
    {
        $booking = DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->join('buses as bus', 't.bus_id', '=', 'bus.id')
            ->select([
                'b.*',
                'r.name as route_name',
                'bus.name as bus_name',
                'bus.model_name as bus_model',
                't.start_time',
                't.end_time',
            ])
            ->where('b.id', $bookingId)
            ->first();

        if (!$booking) {
            return null;
        }

        // Get stop info
        if ($booking->pickup_stop_id) {
            $pickupStop = DB::table('stops')
                ->where('id', $booking->pickup_stop_id)
                ->first();
            $booking->pickup_stop_name = $pickupStop->name ?? null;
            $booking->pickup_stop_address = $pickupStop->address ?? null;
        }

        $dropoffStop = DB::table('stops')
            ->where('id', $booking->dropoff_stop_id)
            ->first();
        $booking->dropoff_stop_name = $dropoffStop->name ?? null;
        $booking->dropoff_stop_address = $dropoffStop->address ?? null;

        // Format pickup display
        $booking->pickup_display = __('client.booking.service.not_available');
        if ($booking->pickup_stop_id && isset($booking->pickup_stop_name)) {
            $booking->pickup_display = $booking->pickup_stop_name;
            if ($booking->pickup_stop_address) {
                $booking->pickup_display .= ' - ' . $booking->pickup_stop_address;
            }
        } elseif (is_null($booking->pickup_stop_id)) {
            $hotelAddress = $this->extractHotelPickupAddress($booking->notes ?? null);
            if ($hotelAddress !== null) {
                $booking->pickup_display = __('client.booking.service.hotel_pickup_display', ['address' => $hotelAddress]);
            }
        }

        return $booking;
    }

    /**
     * Get bookings for admin listing with filters.
     */
    public function getBookingsForAdmin(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->select([
                'b.id',
                'b.booking_code',
                'b.customer_name',
                'b.customer_phone',
                'b.booking_date',
                'b.created_at',
                'b.total_price',
                'b.status',
                'b.confirmed_at',
                'b.payment_method',
                'b.payment_status',
                'b.payment_transaction_id',
                'r.name as route_name',
                't.start_time',
            ]);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('b.booking_code', 'like', "%{$search}%")
                    ->orWhere('b.customer_name', 'like', "%{$search}%")
                    ->orWhere('b.customer_phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('b.status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('b.booking_date', '>=', Carbon::parse($filters['start_date']));
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('b.booking_date', '<=', Carbon::parse($filters['end_date']));
        }

        return $query->orderByDesc('b.created_at')->paginate(15)->withQueryString();
    }

    /**
     * Get booking statistics for admin dashboard (today's stats).
     */
    public function getAdminBookingStats(): array
    {
        $today = Carbon::today();

        $totalToday = DB::table('bookings')
            ->whereDate('created_at', $today)
            ->count();

        $pendingTotal = DB::table('bookings')
            ->where('status', 'pending')
            ->count();

        $revenueToday = (int) DB::table('bookings')
            ->whereDate('created_at', $today)
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        return compact('totalToday', 'pendingTotal', 'revenueToday');
    }

    private function extractHotelPickupAddress(?string $notes): ?string
    {
        if (!is_string($notes) || trim($notes) === '') {
            return null;
        }

        $prefixes = [
            self::NOTE_HOTEL_PICKUP_PREFIX,
            self::LEGACY_NOTE_HOTEL_PICKUP_PREFIX,
        ];

        foreach ($prefixes as $prefix) {
            if (Str::contains($notes, $prefix)) {
                $segment = Str::after($notes, $prefix);
                return trim(Str::before($segment, "\n"));
            }
        }

        return null;
    }
}
