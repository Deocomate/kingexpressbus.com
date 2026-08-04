<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStopRequest;
use App\Http\Requests\Admin\UpdateStopRequest;
use App\Models\Stop;
use App\Support\Admin\AdminResponse;
use App\Support\Admin\DeleteBlockedException;
use App\Support\Admin\DeleteGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StopController extends Controller
{
    public function store(StoreStopRequest $request): RedirectResponse
    {
        Stop::create($request->validated());

        return redirect()->route('admin.locations.index', ['section' => 'stops'])
            ->with('success', 'Đã thêm điểm dừng.');
    }

    public function update(UpdateStopRequest $request, Stop $stop): RedirectResponse
    {
        $stop->update($request->validated());

        return redirect()->route('admin.locations.index', ['section' => 'stops'])
            ->with('success', 'Đã cập nhật điểm dừng.');
    }

    public function destroy(Stop $stop): RedirectResponse
    {
        try {
            DeleteGuard::assertNoBookings(DeleteGuard::stopBookingCount((int) $stop->id), 'điểm dừng');
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        $stop->delete();

        return redirect()->route('admin.locations.index', ['section' => 'stops'])
            ->with('success', 'Đã xóa điểm dừng.');
    }

    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return AdminResponse::bulkResult($request, false, 'Chưa chọn bản ghi nào.');
        }

        try {
            $total = array_sum(array_map(
                fn (int $id) => DeleteGuard::stopBookingCount($id),
                $ids,
            ));
            DeleteGuard::assertNoBookings($total, count($ids) . ' điểm dừng đã chọn');
        } catch (DeleteBlockedException $e) {
            return AdminResponse::bulkResult($request, false, $e->getMessage());
        }

        Stop::whereIn('id', $ids)->delete();

        return AdminResponse::bulkResult($request, true, 'Đã xóa ' . count($ids) . ' điểm dừng.');
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
                Stop::where('id', $id)->update(['priority' => $total - $index]);
            }
        });

        return back()->with('success', 'Đã lưu thứ tự.');
    }
}
