<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BusService;
use App\Services\RouteService;
use App\Services\TripService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    protected TripService $tripService;
    protected BusService $busService;
    protected RouteService $routeService;

    public function __construct(
        TripService $tripService,
        BusService $busService,
        RouteService $routeService
    ) {
        $this->tripService = $tripService;
        $this->busService = $busService;
        $this->routeService = $routeService;
    }

    /**
     * Display a listing of trips grouped by route.
     */
    public function index()
    {
        $data = $this->tripService->getTripsGroupedByRoute();
        $buses = $this->busService->getAllBuses();

        return view('admin.trips.index', [
            'buses' => $buses,
            'startProvinces' => $data['startProvinces'],
            'routesByProvince' => $data['routesByProvince'],
            'tripsByRoute' => $data['tripsByRoute'],
        ]);
    }

    /**
     * Store a newly created trip.
     */
    public function store(Request $request)
    {
        // Clean price format before validation
        $request->merge(['price' => str_replace(',', '', $request->price)]);

        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            $data['priority'] = 999; // Add to the end by default
            $data['is_active'] = true;
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();

            $id = DB::table('trips')->insertGetId($data);

            $newTrip = DB::table('trips as t')
                ->join('buses as b', 't.bus_id', '=', 'b.id')
                ->where('t.id', $id)
                ->select('t.*', 'b.name as bus_name', 'b.model_name as bus_model')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Tạo chuyến xe thành công.',
                'data' => $newTrip,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified trip.
     */
    public function show(string $id)
    {
        $trip = DB::table('trips')->find($id);

        if (!$trip) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy chuyến xe.'], 404);
        }

        return response()->json(['success' => true, 'data' => $trip]);
    }

    /**
     * Update the specified trip.
     */
    public function update(Request $request, string $id)
    {
        // Clean price format before validation
        $request->merge(['price' => str_replace(',', '', $request->price)]);

        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'price' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            $data['updated_at'] = Carbon::now();

            DB::table('trips')->where('id', $id)->update($data);

            $updatedTrip = DB::table('trips as t')
                ->join('buses as b', 't.bus_id', '=', 'b.id')
                ->where('t.id', $id)
                ->select('t.*', 'b.name as bus_name', 'b.model_name as bus_model')
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật chuyến xe thành công.',
                'data' => $updatedTrip,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified trip.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = DB::table('trips')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Xóa chuyến xe thành công.']);
            }

            return response()->json(['success' => false, 'message' => 'Xóa thất bại.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa chuyến vì đang có đặt vé.'], 400);
        }
    }

    /**
     * Update trip order within a route.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'route_id' => 'required|integer|exists:routes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->input('order') as $index => $tripId) {
                    DB::table('trips')
                        ->where('id', $tripId)
                        ->update([
                            'priority' => $index,
                            'route_id' => $request->input('route_id'),
                        ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Cập nhật thứ tự chuyến xe thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra.'], 500);
        }
    }

    /**
     * Toggle trip active status.
     */
    public function toggleStatus(string $id)
    {
        try {
            $trip = DB::table('trips')->where('id', $id)->first();

            if (!$trip) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy chuyến xe.'], 404);
            }

            $newStatus = !$trip->is_active;
            DB::table('trips')->where('id', $id)->update([
                'is_active' => $newStatus,
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'Đã kích hoạt chuyến xe.' : 'Đã tạm dừng chuyến xe.',
                'is_active' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra.'], 500);
        }
    }
}
