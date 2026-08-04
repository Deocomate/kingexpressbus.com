<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDistrictTypeRequest;
use App\Http\Requests\Admin\UpdateDistrictTypeRequest;
use App\Models\DistrictType;
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

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return back()->with('error', 'Chưa chọn bản ghi nào.');
        }

        DistrictType::whereIn('id', $ids)->delete();

        return redirect()->route('admin.locations.index', ['section' => 'district-types'])
            ->with('success', 'Đã xóa ' . count($ids) . ' loại địa điểm.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return back()->with('error', 'Không có dữ liệu để sắp xếp.');
        }

        $total = count($ids);
        DB::transaction(function () use ($ids, $total) {
            foreach ($ids as $index => $id) {
                DistrictType::where('id', $id)->update(['priority' => $total - $index]);
            }
        });

        return back()->with('success', 'Đã lưu thứ tự.');
    }
}
