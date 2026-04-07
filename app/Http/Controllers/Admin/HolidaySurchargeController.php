<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HolidaySurchargeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HolidaySurchargeController extends Controller
{
    protected HolidaySurchargeService $holidaySurchargeService;

    public function __construct(HolidaySurchargeService $holidaySurchargeService)
    {
        $this->holidaySurchargeService = $holidaySurchargeService;
    }

    /**
     * Display list of holiday surcharge rules.
     */
    public function index()
    {
        $rules = $this->holidaySurchargeService->getAllForAdmin();

        return view('admin.holiday-surcharges.index', [
            'rules' => $rules,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $routes = $this->getRouteOptions();

        return view('admin.holiday-surcharges.create', [
            'routes' => $routes,
        ]);
    }

    /**
     * Store a newly created holiday surcharge rule.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $payload = $this->buildPayload($validated, $request);

        try {
            $this->holidaySurchargeService->createForAdmin($payload);
        } catch (\Throwable $exception) {
            Log::error('Holiday surcharge create failed', ['error' => $exception->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Không thể tạo quy tắc phụ thu. Vui lòng kiểm tra dữ liệu và thử lại.');
        }

        return redirect()
            ->route('admin.holiday-surcharges.index')
            ->with('success', 'Tạo quy tắc phụ thu thành công.');
    }

    /**
     * Show edit form.
     */
    public function edit(string $id)
    {
        $rule = $this->holidaySurchargeService->findForAdmin((int) $id);

        if (!$rule) {
            abort(404);
        }

        $routes = $this->getRouteOptions();
        $adjustmentMap = collect($rule->route_adjustments ?? [])->pluck('route_surcharge_amount', 'route_id')->toArray();

        return view('admin.holiday-surcharges.edit', [
            'rule' => $rule,
            'routes' => $routes,
            'adjustmentMap' => $adjustmentMap,
        ]);
    }

    /**
     * Update existing holiday surcharge rule.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePayload($request);
        $payload = $this->buildPayload($validated, $request);

        try {
            $updated = $this->holidaySurchargeService->updateForAdmin((int) $id, $payload);
        } catch (\Throwable $exception) {
            Log::error('Holiday surcharge update failed', ['id' => (int) $id, 'error' => $exception->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Không thể cập nhật quy tắc phụ thu. Vui lòng kiểm tra dữ liệu và thử lại.');
        }

        if (!$updated) {
            return redirect()
                ->route('admin.holiday-surcharges.index')
                ->with('error', 'Không tìm thấy quy tắc phụ thu để cập nhật.');
        }

        return redirect()
            ->route('admin.holiday-surcharges.index')
            ->with('success', 'Cập nhật quy tắc phụ thu thành công.');
    }

    /**
     * Delete holiday surcharge rule.
     */
    public function destroy(string $id)
    {
        $deleted = $this->holidaySurchargeService->deleteForAdmin((int) $id);

        if (!$deleted) {
            return redirect()
                ->route('admin.holiday-surcharges.index')
                ->with('error', 'Không tìm thấy quy tắc phụ thu để xóa.');
        }

        return redirect()
            ->route('admin.holiday-surcharges.index')
            ->with('success', 'Xóa quy tắc phụ thu thành công.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'reason' => 'nullable|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'global_surcharge_amount' => ['required', 'string', 'regex:/^(?=.*\d)[0-9.,\s]+$/'],
            'priority' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
            'route_adjustments' => 'nullable|array',
            'route_adjustments.*.route_surcharge_amount' => ['nullable', 'string', 'regex:/^$|^(?=.*\d)[0-9.,\s]+$/'],
        ]);
    }

    private function buildPayload(array $validated, Request $request): array
    {
        $routeAdjustments = [];
        $rawAdjustments = $request->input('route_adjustments', []);
        $candidateRouteIds = collect($rawAdjustments)
            ->keys()
            ->map(fn($routeId) => (int) $routeId)
            ->filter(fn($routeId) => $routeId > 0)
            ->values()
            ->all();

        $validRouteIdMap = DB::table('routes')
            ->whereIn('id', $candidateRouteIds)
            ->pluck('id')
            ->mapWithKeys(fn($routeId) => [(int) $routeId => true])
            ->all();

        foreach ($rawAdjustments as $routeId => $values) {
            $routeId = (int) $routeId;
            if (!isset($validRouteIdMap[$routeId])) {
                continue;
            }

            $amount = $this->sanitizeCurrency($values['route_surcharge_amount'] ?? '0');

            if ($routeId <= 0 || $amount <= 0) {
                continue;
            }

            $routeAdjustments[] = [
                'route_id' => $routeId,
                'route_surcharge_amount' => $amount,
            ];
        }

        return [
            'name' => $validated['name'],
            'reason' => $validated['reason'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'global_surcharge_amount' => $this->sanitizeCurrency($validated['global_surcharge_amount']),
            'priority' => (int) ($validated['priority'] ?? 0),
            'is_active' => $request->boolean('is_active'),
            'route_adjustments' => $routeAdjustments,
        ];
    }

    private function sanitizeCurrency(string $value): int
    {
        $digitsOnly = preg_replace('/[^0-9]/', '', $value);

        return (int) ($digitsOnly ?: '0');
    }

    private function getRouteOptions()
    {
        return DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select([
                'r.id',
                'r.name',
                'ps.name as start_province_name',
                'pe.name as end_province_name',
            ])
            ->orderByDesc('r.priority')
            ->orderBy('r.name')
            ->get();
    }
}
