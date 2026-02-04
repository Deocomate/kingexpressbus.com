<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusController extends Controller
{
    protected BusService $busService;

    public function __construct(BusService $busService)
    {
        $this->busService = $busService;
    }

    /**
     * Display a listing of buses.
     */
    public function index()
    {
        $services = $this->busService->getAllServices();
        return view('admin.buses.index', compact('services'));
    }

    /**
     * Provide data for DataTables.
     */
    public function list(Request $request)
    {
        $result = $this->busService->getBusesForDataTable($request->all());

        $data = $result['data']->map(function ($bus) {
            return [
                'id' => $bus->id,
                'thumbnail_url' => $bus->thumbnail_url,
                'name' => $bus->name,
                'model_name' => $bus->model_name,
                'seat_count' => $bus->seat_count,
                'priority' => $bus->priority,
                'services' => json_decode($bus->services, true) ?? [],
                'action' => view('admin.buses.partials.actions', ['bus' => $bus])->render(),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data,
            'stats' => $result['stats'] ?? null,
        ]);
    }

    /**
     * Store a newly created bus.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000',
            'model_name' => 'nullable|string|max:1000',
            'seat_map' => 'required|json',
            'services' => 'nullable|array',
            'thumbnail_url' => 'nullable|string|max:1000',
            'image_list_url' => 'nullable|json',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $this->busService->createBus($validator->validated());
            return response()->json(['success' => true, 'message' => 'Thêm xe thành công.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified bus for editing.
     */
    public function show(string $id)
    {
        $bus = $this->busService->getBusById((int) $id);

        if (!$bus) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy xe.'], 404);
        }

        return response()->json(['success' => true, 'data' => $bus]);
    }

    /**
     * Update the specified bus.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000',
            'model_name' => 'nullable|string|max:1000',
            'seat_map' => 'required|json',
            'services' => 'nullable|array',
            'thumbnail_url' => 'nullable|string|max:1000',
            'image_list_url' => 'nullable|json',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $updated = $this->busService->updateBus((int) $id, $validator->validated());

            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Cập nhật thông tin xe thành công.']);
            }

            return response()->json(['success' => false, 'message' => 'Cập nhật thất bại hoặc không có gì thay đổi.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified bus.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = $this->busService->deleteBus((int) $id);

            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Đã xóa xe thành công.']);
            }

            return response()->json(['success' => false, 'message' => 'Xóa thất bại hoặc xe không tồn tại.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa xe vì đang có chuyến xe sử dụng.'], 400);
        }
    }

    /**
     * Get all buses for select options.
     */
    public function all()
    {
        $buses = $this->busService->getBusesForSelect();
        return response()->json($buses);
    }
}
