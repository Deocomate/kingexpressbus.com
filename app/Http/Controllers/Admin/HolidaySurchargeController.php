<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHolidaySurchargeRequest;
use App\Http\Requests\Admin\UpdateHolidaySurchargeRequest;
use App\Models\HolidaySurcharge;
use App\Models\Route;
use App\Services\HolidaySurchargeService;
use App\Support\Admin\AdminResponse;
use App\Support\Admin\Tables\HolidaySurchargeTableConfig;
use App\Support\Admin\TableQuery;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidaySurchargeController extends Controller
{
    public function __construct(private readonly HolidaySurchargeService $service) {}

    public function index(Request $request): View
    {
        $baseQuery = HolidaySurcharge::query()->orderByDesc('priority')->orderByDesc('start_date');

        $config = HolidaySurchargeTableConfig::make();
        $tq     = TableQuery::make($baseQuery, $config)->process($request);

        if ($request->header('X-Partial') === 'table') {
            return view('admin.surcharges._table-rows', [
                'paginator' => $tq->paginator(),
            ]);
        }

        return view('admin.surcharges.index', [
            'paginator' => $tq->paginator(),
            'search'    => $tq->activeSearch(),
        ]);
    }

    public function create(): View
    {
        return view('admin.surcharges.form', [
            'surcharge' => null,
            'routes'    => Route::orderBy('name')->get(),
        ]);
    }

    public function store(StoreHolidaySurchargeRequest $request): RedirectResponse
    {
        $payload = $this->preparePayload($request->validated());

        $this->service->createForAdmin($payload);

        return redirect()->route('admin.surcharges.index')
            ->with('success', 'Đã thêm phụ thu.');
    }

    public function edit(HolidaySurcharge $holidaySurcharge): View
    {
        $surcharge = $this->service->findForAdmin($holidaySurcharge->id);

        return view('admin.surcharges.form', [
            'surcharge' => $surcharge,
            'routes'    => Route::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateHolidaySurchargeRequest $request, HolidaySurcharge $holidaySurcharge): RedirectResponse
    {
        $payload = $this->preparePayload($request->validated());

        $this->service->updateForAdmin($holidaySurcharge->id, $payload);

        return redirect()->route('admin.surcharges.index')
            ->with('success', 'Đã cập nhật phụ thu.');
    }

    public function destroy(HolidaySurcharge $holidaySurcharge): RedirectResponse
    {
        $this->service->deleteForAdmin($holidaySurcharge->id);

        return redirect()->route('admin.surcharges.index')
            ->with('success', 'Đã xóa phụ thu.');
    }

    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return AdminResponse::bulkResult($request, false, 'Không có mục nào được chọn.');
        }

        foreach ($ids as $id) {
            $this->service->deleteForAdmin($id);
        }

        return AdminResponse::bulkResult($request, true, 'Đã xóa ' . count($ids) . ' phụ thu.');
    }

    // -------------------------------------------------------------------------

    private function preparePayload(array $validated): array
    {
        $payload = $validated;

        // Convert d/m/Y → Y-m-d for service
        $payload['start_date'] = Carbon::createFromFormat('d/m/Y', $validated['start_date'])->format('Y-m-d');
        $payload['end_date']   = Carbon::createFromFormat('d/m/Y', $validated['end_date'])->format('Y-m-d');
        $payload['is_active']  = (bool) ($validated['is_active'] ?? false);
        $payload['global_surcharge_amount'] = (int) preg_replace('/\D/', '', (string) ($validated['global_surcharge_amount'] ?? 0));

        // Normalize route adjustments for service
        $adjustments = [];
        foreach ($validated['route_adjustments'] ?? [] as $adj) {
            $adjustments[] = [
                'route_id'               => (int) $adj['route_id'],
                'route_surcharge_amount' => (int) preg_replace('/\D/', '', (string) ($adj['route_surcharge_amount'] ?? 0)),
            ];
        }
        $payload['route_adjustments'] = $adjustments;

        return $payload;
    }
}
