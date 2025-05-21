<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

// Thêm Carbon để xử lý thời gian

class BusRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $routes = DB::table('routes')
            ->join('provinces as ps', 'routes.province_id_start', '=', 'ps.id')
            ->join('provinces as pe', 'routes.province_id_end', '=', 'pe.id')
            ->select('routes.id', 'routes.title', 'ps.name as start_province', 'pe.name as end_province')
            ->orderBy('routes.title', 'asc')
            ->get()
            ->map(function ($route) {
                $route->display_name = "{$route->title} ({$route->start_province} -> {$route->end_province})";
                return $route;
            });

        $selectedRouteId = $request->query('route_id');
        $busRoutes = collect();

        if ($selectedRouteId) {
            $busRoutes = DB::table('bus_routes')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
                ->where('bus_routes.route_id', $selectedRouteId)
                ->select('bus_routes.*', 'buses.name as bus_name') // Lấy tất cả cột từ bus_routes bao gồm cả price mới
                ->orderBy('bus_routes.priority', 'asc')
                ->orderBy('bus_routes.start_at', 'asc')
                ->get();
        }

        return view('kingexpressbus.admin.modules.bus_routes.index', compact('routes', 'selectedRouteId', 'busRoutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedRouteId = $request->query('route_id');
        if (!$selectedRouteId) {
            return redirect()->route('admin.bus_routes.index')->with('error', 'Vui lòng chọn Tuyến đường trước khi tạo Lịch trình.');
        }

        $selectedRoute = DB::table('routes')
            ->join('provinces as ps', 'routes.province_id_start', '=', 'ps.id')
            ->join('provinces as pe', 'routes.province_id_end', '=', 'pe.id')
            ->select('routes.id', 'routes.title', 'ps.name as start_province', 'pe.name as end_province')
            ->where('routes.id', $selectedRouteId)->first();

        if (!$selectedRoute) {
            return redirect()->route('admin.bus_routes.index')->with('error', 'Tuyến đường không hợp lệ.');
        }
        $selectedRoute->display_name = "{$selectedRoute->title} ({$selectedRoute->start_province} -> {$selectedRoute->end_province})";

        $buses = DB::table('buses')->orderBy('name', 'asc')->get(['id', 'name']);

        // <<< Start: Fetch Districts for Stops >>>
        $districtsByProvince = DB::table('districts')
            ->join('provinces', 'districts.province_id', '=', 'provinces.id')
            ->select('districts.id', 'districts.name', 'districts.type', 'provinces.name as province_name')
            ->orderBy('provinces.name', 'asc')
            ->orderBy('districts.priority', 'asc')
            ->orderBy('districts.name', 'asc')
            ->get()
            ->groupBy('province_name');
        // <<< End: Fetch Districts for Stops >>>

        $busRoute = null; // Creating new
        $existingStops = collect(); // No existing stops when creating

        return view('kingexpressbus.admin.modules.bus_routes.createOrEdit', compact(
            'selectedRouteId',
            'selectedRoute',
            'buses',
            'busRoute',
            'districtsByProvince', // Pass districts to view
            'existingStops' // Pass empty collection
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => [
                'required',
                'exists:buses,id',
                Rule::unique('bus_routes')->where(function ($query) use ($request) {
                    return $query->where('route_id', $request->input('route_id'));
                })
            ],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i|after:start_at',
            'price' => 'required|integer|min:0', // Thêm validation cho price
            'detail' => 'required|string',
            'priority' => 'required|integer',
            // <<< Start: Stop Validation >>>
            'stops' => 'nullable|array', // Stops is an array, can be empty
            'stops.*.district_id' => 'required_with:stops|exists:districts,id', // If stops exist, district_id is required
            'stops.*.stop_at' => 'required_with:stops|date_format:H:i', // If stops exist, stop_at is required and in H:i format
            'stops.*.title' => 'nullable|string|max:255', // Optional title
            // <<< End: Stop Validation >>>
        ]);

        // Prepare Bus Route data (exclude stops from validated array for bus_route insert)
        $busRouteData = $validated;
        unset($busRouteData['stops']); // Remove stops data before inserting bus_route

        // Slug Generation
        $baseSlug = Str::slug($busRouteData['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table('bus_routes')->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $busRouteData['slug'] = $slug;
        $busRouteData['created_at'] = now();
        $busRouteData['updated_at'] = now();

        // Insert Bus Route and get ID
        $busRouteId = DB::table('bus_routes')->insertGetId($busRouteData);

        // <<< Start: Save Stops >>>
        if ($request->has('stops') && is_array($request->input('stops'))) {
            $stopsData = [];
            foreach ($request->input('stops') as $stop) {
                // Ensure required fields are present
                if (!empty($stop['district_id']) && !empty($stop['stop_at'])) {
                    $stopsData[] = [
                        'bus_route_id' => $busRouteId,
                        'district_id' => $stop['district_id'],
                        'stop_at' => $stop['stop_at'],
                        'title' => $stop['title'] ?? null,
                        // No timestamps needed for stops table based on schema
                    ];
                }
            }
            if (!empty($stopsData)) {
                DB::table('stops')->insert($stopsData);
            }
        }
        // <<< End: Save Stops >>>

        return redirect()->route('admin.bus_routes.index', ['route_id' => $validated['route_id']])
            ->with('success', 'Lịch trình xe và các điểm dừng đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $busRoute = DB::table('bus_routes')->find($id);
        if (!$busRoute) {
            abort(404);
        }

        $selectedRouteId = $busRoute->route_id;

        $selectedRoute = DB::table('routes')
            ->join('provinces as ps', 'routes.province_id_start', '=', 'ps.id')
            ->join('provinces as pe', 'routes.province_id_end', '=', 'pe.id')
            ->select('routes.id', 'routes.title', 'ps.name as start_province', 'pe.name as end_province')
            ->where('routes.id', $selectedRouteId)->first();

        if ($selectedRoute) {
            $selectedRoute->display_name = "{$selectedRoute->title} ({$selectedRoute->start_province} -> {$selectedRoute->end_province})";
        } else {
            return redirect()->route('admin.bus_routes.index')->with('error', 'Tuyến đường liên kết không tồn tại.');
        }

        $buses = DB::table('buses')->orderBy('name', 'asc')->get(['id', 'name']);

        // <<< Start: Fetch Districts and Existing Stops >>>
        $districtsByProvince = DB::table('districts')
            ->join('provinces', 'districts.province_id', '=', 'provinces.id')
            ->select('districts.id', 'districts.name', 'districts.type', 'provinces.name as province_name')
            ->orderBy('provinces.name', 'asc')
            ->orderBy('districts.priority', 'asc')
            ->orderBy('districts.name', 'asc')
            ->get()
            ->groupBy('province_name');

        $existingStops = DB::table('stops')
            ->where('bus_route_id', $id)
            ->orderBy('stop_at', 'asc') // Order by time
            ->get();
        // <<< End: Fetch Districts and Existing Stops >>>

        return view('kingexpressbus.admin.modules.bus_routes.createOrEdit', compact(
            'selectedRouteId',
            'selectedRoute',
            'buses',
            'busRoute',
            'districtsByProvince', // Pass districts
            'existingStops' // Pass existing stops
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $busRoute = DB::table('bus_routes')->find($id);
        if (!$busRoute) {
            abort(404);
        }

        $validated = $request->validate([
            // Không cho phép sửa route_id ở đây
            'bus_id' => [
                'required',
                'exists:buses,id',
                Rule::unique('bus_routes')->where(function ($query) use ($request, $busRoute) {
                    return $query->where('route_id', $busRoute->route_id); // Use original route_id
                })->ignore($id)
            ],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i|after:start_at',
            'price' => 'required|integer|min:0', // Thêm validation cho price
            'detail' => 'required|string',
            'priority' => 'required|integer',
            // <<< Start: Stop Validation >>>
            'stops' => 'nullable|array',
            'stops.*.district_id' => 'required_with:stops|exists:districts,id',
            'stops.*.stop_at' => 'required_with:stops|date_format:H:i',
            'stops.*.title' => 'nullable|string|max:255',
            // <<< End: Stop Validation >>>
        ]);

        // Prepare Bus Route data for update
        $busRouteData = $validated;
        unset($busRouteData['stops']); // Remove stops data

        // Slug Generation (Only update if title changes, optional)
        if ($busRoute->title !== $busRouteData['title']) {
            $baseSlug = Str::slug($busRouteData['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (DB::table('bus_routes')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $busRouteData['slug'] = $slug;
        }
        $busRouteData['route_id'] = $busRoute->route_id; // Ensure route_id is included
        $busRouteData['updated_at'] = now();

        // Update Bus Route
        DB::table('bus_routes')->where('id', $id)->update($busRouteData);

        // <<< Start: Update Stops (Delete old, Insert new) >>>
        DB::table('stops')->where('bus_route_id', $id)->delete();

        if ($request->has('stops') && is_array($request->input('stops'))) {
            $stopsData = [];
            foreach ($request->input('stops') as $stop) {
                if (!empty($stop['district_id']) && !empty($stop['stop_at'])) {
                    $stopsData[] = [
                        'bus_route_id' => $id, // Use the current bus route ID
                        'district_id' => $stop['district_id'],
                        'stop_at' => $stop['stop_at'],
                        'title' => $stop['title'] ?? null,
                    ];
                }
            }
            if (!empty($stopsData)) {
                DB::table('stops')->insert($stopsData);
            }
        }
        // <<< End: Update Stops >>>

        return redirect()->route('admin.bus_routes.index', ['route_id' => $busRoute->route_id]) // Redirect back to the route's index
        ->with('success', 'Lịch trình xe và các điểm dừng đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $busRoute = DB::table('bus_routes')->find($id);
        if (!$busRoute) {
            return back()->with('error', 'Không tìm thấy Lịch trình xe để xóa.');
        }

        $routeId = $busRoute->route_id; // Get route_id before deleting

        // Deleting bus_route will cascade delete stops due to DB constraint
        DB::table('bus_routes')->where('id', $id)->delete();

        return redirect()->route('admin.bus_routes.index', ['route_id' => $routeId])
            ->with('success', 'Lịch trình xe đã được xóa thành công!');
    }
}
