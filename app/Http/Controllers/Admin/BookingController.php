<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $bookings = $this->bookingService->getBookingsForAdmin($filters);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = $this->bookingService->getBookingDetails((int) $id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }

        // Clean up notes for display (remove hotel part if present)
        $booking->notes_display = $booking->notes ?? '';
        if (Str::contains($booking->notes ?? '', '[Đón tại khách sạn]')) {
            $parts = explode("\n", $booking->notes, 2);
            $booking->notes_display = count($parts) > 1 ? trim(Str::after($parts[1], '[Ghi chú của khách]: ')) : '';
        }
        if (empty(trim((string)$booking->notes_display))) {
            $booking->notes_display = 'Không có';
        }

        return response()->json(['success' => true, 'data' => $booking]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $result = $this->bookingService->updateStatus(
            (int) $id,
            $request->input('status'),
            $request->input('notes')
        );

        if ($result['success']) {
            $updatedBooking = DB::table('bookings')->select('id', 'status', 'notes')->find($id);
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'updated_data' => [
                    'status' => $updatedBooking->status,
                    'notes' => $updatedBooking->notes,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => $result['message']], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = DB::table('bookings')->where('id', $id)->first();
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }

        $deleted = DB::table('bookings')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa đặt vé thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Xóa thất bại, vui lòng thử lại.'], 500);
    }
}
