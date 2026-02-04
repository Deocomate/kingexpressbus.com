<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RouteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RouteController extends Controller
{
    protected RouteService $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    public function index()
    {
        $data = $this->routeService->getRoutesGroupedByProvince();
        $all_provinces_for_modal = $this->routeService->getAllProvinces();
        $all_stops = $this->routeService->getAllStopsWithLocation();

        return view('admin.routes.index', [
            'startProvinces' => $data['startProvinces'],
            'routes' => $data['routes'],
            'all_provinces_for_modal' => $all_provinces_for_modal,
            'all_stops' => $all_stops,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['slug' => Str::slug($request->name)]);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:routes,slug',
            'province_start_id' => 'required|integer|exists:provinces,id',
            'province_end_id' => 'required|integer|exists:provinces,id|different:province_start_id',
            'duration' => 'nullable|string|max:100',
            'distance_km' => 'nullable|integer',
            'price_default' => 'nullable|integer|min:0',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'available_hotel_pickup' => 'nullable|boolean',
            'priority' => 'required|integer',
            'stops_json' => 'nullable|json',
        ], [
            'province_end_id.different' => 'Tỉnh đến phải khác Tỉnh đi.',
            'slug.unique' => 'Tên này đã được sử dụng, vui lòng chọn tên khác.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            
            // Process stops JSON
            if (!empty($data['stops_json'])) {
                $data['stops'] = json_decode($data['stops_json'], true);
            }
            unset($data['stops_json']);
            
            // Clean price
            if (isset($data['price_default'])) {
                $data['price_default'] = (int) str_replace(',', '', $data['price_default']);
            }

            $this->routeService->createRoute($data);
            return response()->json(['success' => true, 'message' => 'Thêm tuyến đường thành công. Vui lòng tải lại trang.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Thêm tuyến đường thất bại: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $route = $this->routeService->getRouteById((int) $id);
        
        if (!$route) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tuyến đường.'], 404);
        }

        // Get route stops
        $route->stops = $this->routeService->getRouteStops((int) $id);
        
        return response()->json(['success' => true, 'data' => $route]);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['slug' => Str::slug($request->name)]);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:routes,slug,' . $id,
            'province_start_id' => 'required|integer|exists:provinces,id',
            'province_end_id' => 'required|integer|exists:provinces,id|different:province_start_id',
            'duration' => 'nullable|string|max:100',
            'distance_km' => 'nullable|integer',
            'price_default' => 'nullable|integer|min:0',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'available_hotel_pickup' => 'nullable|boolean',
            'priority' => 'required|integer',
            'stops_json' => 'nullable|json',
        ], [
            'province_end_id.different' => 'Tỉnh đến phải khác Tỉnh đi.',
            'slug.unique' => 'Tên này đã được sử dụng, vui lòng chọn tên khác.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            
            // Process stops JSON
            if (!empty($data['stops_json'])) {
                $data['stops'] = json_decode($data['stops_json'], true);
            }
            unset($data['stops_json']);
            
            // Clean price
            if (isset($data['price_default'])) {
                $data['price_default'] = (int) str_replace(',', '', $data['price_default']);
            }

            $this->routeService->updateRoute((int) $id, $data);
            return response()->json(['success' => true, 'message' => 'Cập nhật tuyến đường thành công. Vui lòng tải lại trang.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật thất bại: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->routeService->deleteRoute((int) $id);
            
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Đã xóa tuyến đường thành công.']);
            }
            return response()->json(['success' => false, 'message' => 'Xóa thất bại.'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa tuyến đường vì đang có chuyến xe sử dụng.'], 400);
        }
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), ['order' => 'required|array']);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }

        try {
            $this->routeService->updateOrder($request->input('order'));
            return response()->json(['success' => true, 'message' => 'Cập nhật thứ tự thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra.'], 500);
        }
    }
    
    /**
     * Get all routes for select options.
     */
    public function all()
    {
        $data = $this->routeService->getRoutesGroupedByProvince();
        $routes = collect();
        
        foreach ($data['routes'] as $routeGroup) {
            foreach ($routeGroup as $route) {
                $routes->push([
                    'id' => $route->id,
                    'text' => $route->name . ' (' . $route->start_province_name . ' → ' . $route->end_province_name . ')',
                ]);
            }
        }
        
        return response()->json($routes);
    }
}
