<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDistrictTypeRequest;
use App\Http\Requests\Admin\UpdateDistrictTypeRequest;
use App\Models\DistrictType;
use App\Support\Admin\AdminResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrictTypeController extends Controller
{
    public function store(StoreDistrictTypeRequest $request): RedirectResponse
    {
        DistrictType::create($request->validated());

        return redirect()->route('admin.locations.index', ['section' => 'district-types'])
            ->with('success', 'Đã thêm loại địa điểm.');
    }

    public function update(UpdateDistrictTypeRequest $request, DistrictType $districtType): RedirectResponse
    {
        $districtType->update($request->validated());

        return redirect()->route('admin.locations.index', ['section' => 'district-types'])
            ->with('success', 'Đã cập nhật loại địa điểm.');
    }

    public function destroy(DistrictType $districtType): RedirectResponse
    {
        $districtType->delete();

        return redirect()->route('admin.locations.index', ['section' => 'district-types'])
            ->with('success', 'Đã xóa loại địa điểm.');
    }

    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return AdminResponse::bulkResult($request, false, 'Chưa chọn bản ghi nào.');
        }

        DistrictType::whereIn('id', $ids)->delete();

        return AdminResponse::bulkResult($request, true, 'Đã xóa ' . count($ids) . ' loại địa điểm.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return response()->json(['ok' => false, 'message' => 'Không có dữ liệu để sắp xếp.'], 422);
        }

        $total = count($ids);
        DB::transaction(function () use ($ids, $total) {
            foreach ($ids as $index => $id) {
                DistrictType::where('id', $id)->update(['priority' => $total - $index]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
