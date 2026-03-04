<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\TripService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected BookingService $bookingService;
    protected TripService $tripService;

    public function __construct(BookingService $bookingService, TripService $tripService)
    {
        $this->bookingService = $bookingService;
        $this->tripService = $tripService;
    }

    public function create(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|integer|exists:trips,id',
            'date' => 'required|string',
        ]);

        $tripId = (int) $request->input('trip_id');
        $bookingDate = $this->parseBookingDate($request->input('date', now()->format('Y-m-d')));

        // Get trip details using service
        $trip = $this->tripService->getTripDetails($tripId);

        abort_if(!$trip || !$trip->is_active, 404, __('client.booking.create.trip_not_found'));

        // Calculate available seats
        $availableSeats = $this->tripService->calculateAvailableSeats(
            $tripId,
            $bookingDate->format('Y-m-d'),
            $trip->seat_count
        );

        // Get route stops
        $stops = $this->tripService->getRouteStops($trip->route_id);

        $paymentMethods = [
            [
                'key' => 'online_banking',
                'label' => __('client.booking.create.payment_online_label'),
                'description' => __('client.booking.create.payment_online_desc'),
            ],
            [
                'key' => 'cash_on_pickup',
                'label' => __('client.booking.create.payment_cash_label'),
                'description' => __('client.booking.create.payment_cash_desc'),
            ],
        ];

        $user = Auth::user();

        return view('client.booking.create', [
            'trip' => $trip,
            'bookingDate' => $bookingDate,
            'stops' => $stops,
            'services' => $trip->bus_services,
            'busImages' => $trip->bus_images,
            'paymentMethods' => $paymentMethods,
            'availableSeats' => $availableSeats,
            'user' => $user,
            'title' => __('client.booking.create.meta_title'),
            'description' => __('client.booking.create.meta_description'),
        ]);
    }

    public function store(Request $request)
    {
        $isHotelPickup = $request->input('pickup_stop_id') === 'hotel_pickup';

        $rules = [
            'trip_id' => 'required|integer|exists:trips,id',
            'booking_date' => 'required|date_format:d/m/Y',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_email' => 'required|email|max:255',
            'pickup_stop_id' => 'required|string',
            'dropoff_stop_id' => 'required|integer|exists:stops,id',
            'total_price' => 'required|integer|min:0',
            'payment_method' => 'required|string|in:cash_on_pickup,online_banking',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($isHotelPickup) {
            $rules['hotel_pickup_address'] = 'required|string|max:1000';
            $rules['pickup_stop_id'] = 'required|in:hotel_pickup';
        } else {
            $rules['pickup_stop_id'] = 'required|integer|exists:stops,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $bookingDateForDb = Carbon::createFromFormat('d/m/Y', $validated['booking_date'])->format('Y-m-d');

        try {
            $bookingData = [
                'trip_id' => $validated['trip_id'],
                'booking_date' => $bookingDateForDb,
                'quantity' => $validated['quantity'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'pickup_stop_id' => $isHotelPickup ? null : $validated['pickup_stop_id'],
                'dropoff_stop_id' => $validated['dropoff_stop_id'],
                'total_price' => $validated['total_price'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'user_id' => Auth::id(),
                'is_hotel_pickup' => $isHotelPickup,
                'hotel_pickup_address' => $validated['hotel_pickup_address'] ?? null,
            ];

            $result = $this->bookingService->createBooking($bookingData);

            if ($result['success']) {
                // Send confirmation email
                $this->bookingService->sendConfirmationEmail($result['booking_id']);
                
                return redirect()->route('client.booking.success')->with('booking_id', $result['booking_id']);
            }

            return back()->with('error', $result['message'])->withInput();
        } catch (\Exception $e) {
            Log::error('Đặt vé phía client thất bại', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function success()
    {
        $bookingId = session('booking_id');
        
        if (!$bookingId) {
            return redirect()->route('client.home');
        }

        $booking = DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->join('buses as bus', 't.bus_id', '=', 'bus.id')
            ->leftJoin('stops as p_stop', 'b.pickup_stop_id', '=', 'p_stop.id')
            ->join('stops as d_stop', 'b.dropoff_stop_id', '=', 'd_stop.id')
            ->select([
                'b.id',
                'b.booking_code',
                'b.customer_name',
                'b.customer_phone',
                'b.customer_email',
                'b.booking_date',
                'b.quantity',
                'b.total_price',
                'b.payment_method',
                'b.payment_status',
                'b.notes',
                'b.pickup_stop_id',
                't.start_time',
                't.end_time',
                'r.name as route_name',
                'r.slug as route_slug',
                'bus.name as bus_name',
                'bus.model_name as bus_model',
                'p_stop.name as pickup_name',
                'p_stop.address as pickup_address',
                'd_stop.name as dropoff_name',
                'd_stop.address as dropoff_address',
            ])
            ->where('b.id', $bookingId)
            ->first();

        return view('client.booking.success', [
            'booking' => $booking,
            'title' => __('client.booking.success.meta_title'),
            'description' => __('client.booking.success.meta_description'),
        ]);
    }

    private function parseBookingDate(string $value): Carbon
    {
        $patterns = ['d-m-Y', 'd/m/Y', 'Y-m-d'];
        
        foreach ($patterns as $pattern) {
            try {
                return Carbon::createFromFormat($pattern, $value)->startOfDay();
            } catch (\Throwable $exception) {
                // Continue to next pattern
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $exception) {
            abort(400, __('client.booking.create.invalid_date'));
        }
    }
}
