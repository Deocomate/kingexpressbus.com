<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HolidaySurchargeService
{
    private array $adjustmentCache = [];

    /**
     * Calculate full price breakdown by trip id and travel date.
     */
    public function calculateBreakdownByTripId(int $tripId, string $travelDate): array
    {
        $trip = DB::table('trips')
            ->where('id', $tripId)
            ->select('id', 'route_id', 'price')
            ->first();

        if (!$trip) {
            return $this->emptyBreakdown($travelDate);
        }

        return $this->calculateBreakdownByRouteAndBasePrice(
            (int) $trip->route_id,
            (int) ($trip->price ?? 0),
            $travelDate
        );
    }

    /**
     * Calculate full price breakdown by route and base price.
     */
    public function calculateBreakdownByRouteAndBasePrice(int $routeId, int $baseUnitPrice, string $travelDate): array
    {
        $normalizedDate = $this->normalizeDate($travelDate);

        if (!$this->isSchemaReady()) {
            return $this->baseOnlyBreakdown($normalizedDate, $baseUnitPrice);
        }

        $adjustments = $this->resolveAdjustmentsByRouteAndDate($routeId, $normalizedDate);

        $totalSurcharge = $adjustments['global_surcharge_unit'] + $adjustments['route_surcharge_unit'];

        return [
            'travel_date' => $normalizedDate,
            'base_unit_price' => max(0, $baseUnitPrice),
            'global_surcharge_unit' => $adjustments['global_surcharge_unit'],
            'route_surcharge_unit' => $adjustments['route_surcharge_unit'],
            'total_surcharge_unit' => $totalSurcharge,
            'final_unit_price' => max(0, $baseUnitPrice + $totalSurcharge),
            'has_surcharge' => $totalSurcharge > 0,
            'surcharge_reasons' => $adjustments['surcharge_reasons'],
            'surcharge_reason_snapshot' => $adjustments['surcharge_reason_snapshot'],
        ];
    }

    /**
     * Get surcharge rules for admin list with route adjustment counts.
     */
    public function getAllForAdmin(): Collection
    {
        return DB::table('holiday_surcharges as hs')
            ->select([
                'hs.*',
                DB::raw('(SELECT COUNT(*) FROM holiday_surcharge_routes hsr WHERE hsr.holiday_surcharge_id = hs.id) as route_adjustments_count'),
            ])
            ->orderByDesc('hs.priority')
            ->orderByDesc('hs.start_date')
            ->get();
    }

    /**
     * Find one surcharge rule with route adjustments for admin edit.
     */
    public function findForAdmin(int $id): ?object
    {
        $rule = DB::table('holiday_surcharges')->where('id', $id)->first();

        if (!$rule) {
            return null;
        }

        $rule->route_adjustments = DB::table('holiday_surcharge_routes as hsr')
            ->join('routes as r', 'hsr.route_id', '=', 'r.id')
            ->where('hsr.holiday_surcharge_id', $id)
            ->select([
                'hsr.id',
                'hsr.route_id',
                'hsr.route_surcharge_amount',
                'r.name as route_name',
            ])
            ->orderBy('r.name')
            ->get();

        return $rule;
    }

    /**
     * Create a surcharge rule and sync route adjustments.
     */
    public function createForAdmin(array $payload): int
    {
        return DB::transaction(function () use ($payload) {
            $ruleId = DB::table('holiday_surcharges')->insertGetId([
                'name' => $payload['name'],
                'reason' => $payload['reason'] ?? null,
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'global_surcharge_amount' => $payload['global_surcharge_amount'],
                'is_active' => $payload['is_active'] ?? true,
                'priority' => $payload['priority'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->syncRouteAdjustments($ruleId, $payload['route_adjustments'] ?? []);

            $this->flushAdjustmentCache();

            return $ruleId;
        });
    }

    /**
     * Update a surcharge rule and sync route adjustments.
     */
    public function updateForAdmin(int $id, array $payload): bool
    {
        return DB::transaction(function () use ($id, $payload) {
            DB::table('holiday_surcharges')
                ->where('id', $id)
                ->update([
                    'name' => $payload['name'],
                    'reason' => $payload['reason'] ?? null,
                    'start_date' => $payload['start_date'],
                    'end_date' => $payload['end_date'],
                    'global_surcharge_amount' => $payload['global_surcharge_amount'],
                    'is_active' => $payload['is_active'] ?? true,
                    'priority' => $payload['priority'] ?? 0,
                    'updated_at' => now(),
                ]);

            $this->syncRouteAdjustments($id, $payload['route_adjustments'] ?? []);

            $this->flushAdjustmentCache();

            return true;
        });
    }

    /**
     * Delete surcharge rule.
     */
    public function deleteForAdmin(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            DB::table('holiday_surcharge_routes')
                ->where('holiday_surcharge_id', $id)
                ->delete();

            $deleted = DB::table('holiday_surcharges')
                ->where('id', $id)
                ->delete() > 0;

            $this->flushAdjustmentCache();

            return $deleted;
        });
    }

    private function syncRouteAdjustments(int $holidaySurchargeId, array $routeAdjustments): void
    {
        DB::table('holiday_surcharge_routes')
            ->where('holiday_surcharge_id', $holidaySurchargeId)
            ->delete();

        if (empty($routeAdjustments)) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($routeAdjustments as $adjustment) {
            $routeId = isset($adjustment['route_id']) ? (int) $adjustment['route_id'] : 0;
            $amount = isset($adjustment['route_surcharge_amount'])
                ? (int) $adjustment['route_surcharge_amount']
                : 0;

            if ($routeId <= 0 || $amount < 0) {
                continue;
            }

            $rows[$routeId] = [
                'holiday_surcharge_id' => $holidaySurchargeId,
                'route_id' => $routeId,
                'route_surcharge_amount' => $amount,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('holiday_surcharge_routes')->insert(array_values($rows));
        }
    }

    private function resolveAdjustmentsByRouteAndDate(int $routeId, string $normalizedDate): array
    {
        $cacheKey = $routeId . '|' . $normalizedDate;

        if (array_key_exists($cacheKey, $this->adjustmentCache)) {
            return $this->adjustmentCache[$cacheKey];
        }

        $rules = DB::table('holiday_surcharges')
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $normalizedDate)
            ->whereDate('end_date', '>=', $normalizedDate)
            ->orderByDesc('priority')
            ->orderBy('start_date')
            ->select([
                'id',
                'name',
                'reason',
                'global_surcharge_amount',
            ])
            ->get();

        if ($rules->isEmpty()) {
            $empty = [
                'global_surcharge_unit' => 0,
                'route_surcharge_unit' => 0,
                'surcharge_reasons' => [],
                'surcharge_reason_snapshot' => null,
            ];

            $this->adjustmentCache[$cacheKey] = $empty;

            return $empty;
        }

        $ruleIds = $rules->pluck('id')->all();

        $routeAdjustments = DB::table('holiday_surcharge_routes')
            ->where('route_id', $routeId)
            ->whereIn('holiday_surcharge_id', $ruleIds)
            ->select([
                'holiday_surcharge_id',
                'route_surcharge_amount',
            ])
            ->get()
            ->groupBy('holiday_surcharge_id');

        $globalSurchargeUnit = 0;
        $routeSurchargeUnit = 0;
        $reasons = [];

        foreach ($rules as $rule) {
            $ruleGlobalAmount = (int) ($rule->global_surcharge_amount ?? 0);
            $ruleRouteAmount = (int) ($routeAdjustments->get($rule->id)?->sum('route_surcharge_amount') ?? 0);

            $globalSurchargeUnit += $ruleGlobalAmount;
            $routeSurchargeUnit += $ruleRouteAmount;

            if (($ruleGlobalAmount + $ruleRouteAmount) <= 0) {
                continue;
            }

            $segments = [];
            if ($ruleGlobalAmount > 0) {
                $segments[] = 'global +' . number_format($ruleGlobalAmount) . 'đ';
            }
            if ($ruleRouteAmount > 0) {
                $segments[] = 'route +' . number_format($ruleRouteAmount) . 'đ';
            }

            $label = trim((string) ($rule->name ?? 'Holiday surcharge'));
            $reasons[] = $label . ' (' . implode(', ', $segments) . ')';
        }

        $result = [
            'global_surcharge_unit' => $globalSurchargeUnit,
            'route_surcharge_unit' => $routeSurchargeUnit,
            'surcharge_reasons' => $reasons,
            'surcharge_reason_snapshot' => empty($reasons) ? null : implode('; ', $reasons),
        ];

        $this->adjustmentCache[$cacheKey] = $result;

        return $result;
    }

    private function flushAdjustmentCache(): void
    {
        $this->adjustmentCache = [];
    }

    private function normalizeDate(string $travelDate): string
    {
        return Carbon::parse($travelDate)->format('Y-m-d');
    }

    private function isSchemaReady(): bool
    {
        return Schema::hasTable('holiday_surcharges')
            && Schema::hasTable('holiday_surcharge_routes');
    }

    private function emptyBreakdown(string $travelDate): array
    {
        return [
            'travel_date' => $this->normalizeDate($travelDate),
            'base_unit_price' => 0,
            'global_surcharge_unit' => 0,
            'route_surcharge_unit' => 0,
            'total_surcharge_unit' => 0,
            'final_unit_price' => 0,
            'has_surcharge' => false,
            'surcharge_reasons' => [],
            'surcharge_reason_snapshot' => null,
        ];
    }

    private function baseOnlyBreakdown(string $travelDate, int $baseUnitPrice): array
    {
        return [
            'travel_date' => $travelDate,
            'base_unit_price' => max(0, $baseUnitPrice),
            'global_surcharge_unit' => 0,
            'route_surcharge_unit' => 0,
            'total_surcharge_unit' => 0,
            'final_unit_price' => max(0, $baseUnitPrice),
            'has_surcharge' => false,
            'surcharge_reasons' => [],
            'surcharge_reason_snapshot' => null,
        ];
    }
}
