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

        $busRoute = null; // Creating new

        return view('kingexpressbus.admin.modules.bus_routes.createOrEdit', compact(
            'selectedRouteId',
            'selectedRoute',
            'buses',
            'busRoute'
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
            ],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i|after:start_at',
            'price' => 'required|integer|min:0', // Thêm validation cho price
            'detail' => 'required|string',
            'priority' => 'required|integer',
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table('bus_routes')->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('bus_routes')->insert($validated);

        return redirect()->route('admin.bus_routes.index', ['route_id' => $validated['route_id']])
            ->with('success', 'Lịch trình xe đã được tạo thành công!');
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

        return view('kingexpressbus.admin.modules.bus_routes.createOrEdit', compact(
            'selectedRouteId',
            'selectedRoute',
            'buses',
            'busRoute'
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
            'bus_id' => [
                'required',
                'exists:buses,id',
            ],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i|after:start_at',
            'price' => 'required|integer|min:0',
            'detail' => 'required|string',
            'priority' => 'required|integer',
        ]);

        $busRouteData = $validated;

        if ($busRoute->title !== $busRouteData['title']) {
            $baseSlug = Str::slug($busRouteData['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (DB::table('bus_routes')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $busRouteData['slug'] = $slug;
        }
        $busRouteData['updated_at'] = now();

        DB::table('bus_routes')->where('id', $id)->update($busRouteData);

        return redirect()->route('admin.bus_routes.index', ['route_id' => $busRoute->route_id])
            ->with('success', 'Lịch trình xe đã được cập nhật thành công!');
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

        $routeId = $busRoute->route_id;

        DB::table('bus_routes')->where('id', $id)->delete();

        return redirect()->route('admin.bus_routes.index', ['route_id' => $routeId])
            ->with('success', 'Lịch trình xe đã được xóa thành công!');
    }
}
