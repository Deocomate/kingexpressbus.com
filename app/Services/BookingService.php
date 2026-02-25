<?php

namespace App\Services;

use App\Helpers\SystemHelper;
use App\Mail\BookingApprovedMail;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmMail;
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
                throw new \Exception('Chuyến xe không tồn tại hoặc đã ngừng hoạt động.');
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
                throw new \Exception("Không đủ ghế trống. Yêu cầu: {$requestedQuantity}, Còn trống: {$availableSeats}");
            }

            // Process hotel pickup if applicable
            $pickupStopId = $data['pickup_stop_id'] ?? null;
            $bookingNotes = isset($data['notes']) ? strip_tags($data['notes']) : null;

            if (isset($data['is_hotel_pickup']) && $data['is_hotel_pickup']) {
                $hotelAddress = strip_tags($data['hotel_pickup_address'] ?? '');
                $hotelNote = "[Đón tại khách sạn]: " . $hotelAddress;
                $bookingNotes = $bookingNotes ? $hotelNote . "\n[Ghi chú của khách]: " . $bookingNotes : $hotelNote;
                $pickupStopId = null;
            }

            // Create the booking
            $bookingId = DB::table('bookings')->insertGetId([
                'user_id' => $data['user_id'] ?? null,
                'trip_id' => $data['trip_id'],
                'booking_date' => $data['booking_date'],
                'booking_code' => SystemHelper::generateBookingCode(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'],
                'pickup_stop_id' => $pickupStopId,
                'dropoff_stop_id' => $data['dropoff_stop_id'],
                'quantity' => $requestedQuantity,
                'total_price' => $data['total_price'],
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
                'message' => 'Đặt vé thành công.',
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
                Log::error('Không thể chuẩn bị dữ liệu mail cho booking ID: ' . $bookingId);
                return false;
            }

            // Send to customer
            Mail::to($mailDetails['customer_email'])->queue(new BookingConfirmMail($mailDetails));

            // Send to admin
            $adminEmail = config('mail.admin_email', 'kingexpressbus@gmail.com');
            Mail::to($adminEmail)->queue(new BookingConfirmMail($mailDetails));

            return true;
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi email xác nhận đặt vé', [
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
        $result['web_phone'] = $webProfile->hotline ?? $webProfile->phone ?? 'N/A';
        $result['web_email'] = $webProfile->email ?? 'N/A';
        $result['web_link'] = url('/');
        $result['web_logo'] = !empty($webProfile->logo_url) ? url($webProfile->logo_url) : null;

        $result['departure_date'] = Carbon::parse($result['booking_date'])->format('d/m/Y');
        $result['start_time'] = Carbon::parse($result['start_time'])->format('H:i');
        $result['bus_type_name'] = $result['bus_model_name'] ?? 'Đang cập nhật';

        // Handle hotel pickup display
        if (is_null($result['pickup_stop_id']) && Str::contains($result['notes'], '[Đón tại khách sạn]')) {
            $result['pickup_info'] = Str::after($result['notes'], '[Đón tại khách sạn]: ');
        } else {
            $result['pickup_info'] = sprintf('%s - %s', $result['pickup_name'] ?? 'N/A', $result['pickup_address'] ?? 'N/A');
        }

        $result['needs_bank_transfer_info'] = ($result['payment_method'] === 'online_banking' && $result['payment_status'] !== 'paid');

        return $result;
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(int $bookingId, ?string $reason = null, ?int $adminUserId = null): array
    {
        $booking = DB::table('bookings')->where('id', $bookingId)->first();

        if (!$booking) {
            return ['success' => false, 'message' => 'Không tìm thấy đặt vé.'];
        }

        if ($booking->status === 'cancelled') {
            return ['success' => false, 'message' => 'Đặt vé đã được hủy trước đó.'];
        }

        $updateData = [
            'status' => 'cancelled',
            'updated_at' => now(),
        ];

        if ($reason) {
            $existingNotes = $booking->notes ?? '';
            $cancelNote = $adminUserId ? '[Lý do hủy Admin]: ' : '[Lý do hủy]: ';
            $updateData['notes'] = $existingNotes . "\n" . $cancelNote . $reason;
        }

        DB::table('bookings')->where('id', $bookingId)->update($updateData);

        // Auto gửi mail thông báo hủy vé cho khách
        $this->sendCancellationEmail($bookingId, $reason);

        return ['success' => true, 'message' => 'Hủy đặt vé thành công.'];
    }

    /**
     * Send booking cancellation email to customer.
     */
    public function sendCancellationEmail(int $bookingId, ?string $reason = null): bool
    {
        try {
            $mailDetails = $this->prepareMailDetails($bookingId);

            if (!$mailDetails) {
                Log::error('Không thể chuẩn bị dữ liệu mail hủy vé cho booking ID: ' . $bookingId);
                return false;
            }

            $mailDetails['cancel_reason'] = $reason ?: 'Không có lý do cụ thể';

            // Gửi mail thông báo hủy cho khách hàng
            Mail::to($mailDetails['customer_email'])->queue(new BookingCancelledMail($mailDetails));

            // Gửi mail thông báo cho admin
            $adminEmail = config('mail.admin_email', 'kingexpressbus@gmail.com');
            Mail::to($adminEmail)->queue(new BookingCancelledMail($mailDetails));

            Log::info('Đã gửi mail hủy vé thành công', [
                'booking_id' => $bookingId,
                'booking_code' => $mailDetails['booking_code'] ?? 'N/A',
                'customer_email' => $mailDetails['customer_email'] ?? 'N/A',
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi email hủy vé', [
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
                Log::error('Không thể chuẩn bị dữ liệu mail xác nhận vé cho booking ID: ' . $bookingId);
                return false;
            }

            Mail::to($mailDetails['customer_email'])->queue(new BookingApprovedMail($mailDetails));

            $adminEmail = config('mail.admin_email', 'kingexpressbus@gmail.com');
            Mail::to($adminEmail)->queue(new BookingApprovedMail($mailDetails));

            Log::info('Đã gửi mail xác nhận vé thành công', [
                'booking_id' => $bookingId,
                'booking_code' => $mailDetails['booking_code'] ?? 'N/A',
                'customer_email' => $mailDetails['customer_email'] ?? 'N/A',
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi email xác nhận vé', [
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
            return ['success' => false, 'message' => 'Không tìm thấy đặt vé.'];
        }

        $updateData = [
            'status' => $status,
            'updated_at' => now(),
        ];

        // Handle cancellation notes
        if ($status === 'cancelled' && $notes) {
            $existingNotes = $booking->notes ?? '';
            if (!Str::contains($existingNotes, '[Lý do hủy Admin]')) {
                $updateData['notes'] = $existingNotes . "\n[Lý do hủy Admin]: " . trim($notes);
            }
        }

        DB::table('bookings')->where('id', $bookingId)->update($updateData);

        // Auto gửi mail xác nhận vé khi chuyển từ pending sang confirmed
        if ($status === 'confirmed' && $booking->status === 'pending') {
            $this->sendApprovalEmail($bookingId);
        }

        // Auto gửi mail thông báo hủy vé khi chuyển trạng thái sang cancelled
        if ($status === 'cancelled') {
            $this->sendCancellationEmail($bookingId, $notes);
        }

        return ['success' => true, 'message' => 'Cập nhật trạng thái thành công.'];
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
        $booking->pickup_display = 'N/A';
        if ($booking->pickup_stop_id && isset($booking->pickup_stop_name)) {
            $booking->pickup_display = $booking->pickup_stop_name;
            if ($booking->pickup_stop_address) {
                $booking->pickup_display .= ' - ' . $booking->pickup_stop_address;
            }
        } elseif (is_null($booking->pickup_stop_id) && Str::contains($booking->notes ?? '', '[Đón tại khách sạn]')) {
            $hotelAddress = Str::after($booking->notes, '[Đón tại khách sạn]: ');
            $hotelAddress = Str::before($hotelAddress, "\n");
            $booking->pickup_display = 'Đón tại khách sạn: ' . trim($hotelAddress);
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
}
