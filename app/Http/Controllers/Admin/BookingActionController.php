<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;

/**
 * Handles the three booking status-transition actions.
 * All transitions go through BookingService — never write status directly.
 * Server-side status checks prevent invalid transitions regardless of what the client sends.
 */
class BookingActionController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    /** POST /quan-tri/dat-ve/{booking}/xac-nhan */
    public function confirm(int $booking): RedirectResponse
    {
        $b = Booking::findOrFail($booking);

        if ($b->status !== BookingStatus::Pending) {
            return back()->with('error', 'Chỉ có thể xác nhận đặt vé ở trạng thái chờ xác nhận.');
        }

        $result = $this->bookingService->updateStatus($booking, 'confirmed');

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /** POST /quan-tri/dat-ve/{booking}/huy */
    public function cancel(CancelBookingRequest $request, int $booking): RedirectResponse
    {
        $b = Booking::findOrFail($booking);

        if (in_array($b->status, [BookingStatus::Cancelled, BookingStatus::Completed], true)) {
            return back()->with('error', 'Không thể hủy đặt vé đã hoàn thành hoặc đã bị hủy.');
        }

        $result = $this->bookingService->cancelBooking(
            $booking,
            $request->resolvedReason(),
            auth()->id()
        );

        return redirect()
            ->route('admin.bookings.index', request()->only(['tab', 'search', 'page', 'filter', 'sort', 'direction']))
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /** POST /quan-tri/dat-ve/{booking}/hoan-thanh */
    public function complete(int $booking): RedirectResponse
    {
        $b = Booking::findOrFail($booking);

        if ($b->status !== BookingStatus::Confirmed) {
            return back()->with('error', 'Chỉ có thể hoàn thành đặt vé đã xác nhận.');
        }

        $result = $this->bookingService->updateStatus($booking, 'completed');

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }
}
