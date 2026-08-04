<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTripBlockRequest;
use App\Http\Requests\Admin\UpdateTripBlockRequest;
use App\Models\Route;
use App\Models\Trip;
use App\Models\TripBlock;
use App\Support\Admin\AdminResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripBlockController extends Controller
{
    public function create(): View
    {
        return view('admin.trips.form-block', [
            'block'   => null,
            'routes'  => Route::orderBy('name')->get(),
            'routeId' => null,
            'trips'   => collect(),
        ]);
    }

    public function store(StoreTripBlockRequest $request): RedirectResponse
    {
        $data = collect($request->validated())
            ->only(['trip_id', 'start_date', 'end_date', 'block_type', 'note'])
            ->all();

        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
        $data['end_date']   = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d');

        TripBlock::create($data);

        return redirect()->route('admin.trips.index', ['section' => 'blocks'])
            ->with('success', 'Đã thêm khóa chuyến.');
    }

    public function edit(TripBlock $tripBlock): View
    {
        $tripBlock->load(['trip.route', 'trip.bus']);

        $routeId = $tripBlock->trip?->route_id;
        $trips   = $routeId
            ? Trip::with('bus')->where('route_id', $routeId)->orderByDesc('priority')->orderBy('start_time')->get()
            : collect();

        return view('admin.trips.form-block', [
            'block'   => $tripBlock,
            'routes'  => Route::orderBy('name')->get(),
            'routeId' => $routeId,
            'trips'   => $trips,
        ]);
    }

    public function update(UpdateTripBlockRequest $request, TripBlock $tripBlock): RedirectResponse
    {
        $data = collect($request->validated())
            ->only(['trip_id', 'start_date', 'end_date', 'block_type', 'note'])
            ->all();

        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
        $data['end_date']   = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d');

        $tripBlock->update($data);

        return redirect()->route('admin.trips.index', ['section' => 'blocks'])
            ->with('success', 'Đã cập nhật khóa chuyến.');
    }

    public function destroy(TripBlock $tripBlock): RedirectResponse
    {
        $tripBlock->delete();

        return redirect()->route('admin.trips.index', ['section' => 'blocks'])
            ->with('success', 'Đã xóa khóa chuyến.');
    }

    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return AdminResponse::bulkResult($request, false, 'Không có khóa chuyến nào được chọn.');
        }

        TripBlock::whereIn('id', $ids)->delete();

        return AdminResponse::bulkResult($request, true, 'Đã xóa ' . count($ids) . ' khóa chuyến.');
    }
}
