<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBusServiceRequest;
use App\Http\Requests\Admin\UpdateBusServiceRequest;
use App\Models\BusService;
use App\Support\Admin\DeleteBlockedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BusServiceController extends Controller
{
    public function store(StoreBusServiceRequest $request): JsonResponse
    {
        $service = BusService::create($request->only('name', 'icon'));

        return response()->json([
            'ok'      => true,
            'service' => ['id' => $service->id, 'name' => $service->name, 'icon' => $service->icon],
        ], 201);
    }

    public function update(UpdateBusServiceRequest $request, int $service): JsonResponse
    {
        $model = BusService::findOrFail($service);
        $model->update($request->only('name', 'icon'));

        return response()->json([
            'ok'      => true,
            'service' => ['id' => $model->id, 'name' => $model->name, 'icon' => $model->icon],
        ]);
    }

    public function destroy(int $service): JsonResponse
    {
        $model = BusService::findOrFail($service);

        $busCount = (int) DB::table('bus_bus_service')
            ->where('bus_service_id', $model->id)
            ->count();

        if ($busCount > 0) {
            return response()->json([
                'ok'      => false,
                'message' => "Không thể xóa dịch vụ \"{$model->name}\": đang gắn với {$busCount} xe.",
            ], 422);
        }

        $model->delete();

        return response()->json(['ok' => true]);
    }
}
