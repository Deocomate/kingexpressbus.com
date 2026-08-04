<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRouteStopRequest;
use App\Http\Requests\Admin\UpdateRouteStopRequest;
use App\Models\Route;
use App\Models\RouteStop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteStopController extends Controller
{
    public function store(StoreRouteStopRequest $request, int $route): JsonResponse
    {
        $routeModel = Route::findOrFail($route);

        // Assign lowest priority (will appear at bottom of desc list)
        $minPriority = (int) DB::table('route_stops')
            ->where('route_id', $routeModel->id)
            ->min('priority');

        $stop = RouteStop::create([
            'route_id'  => $routeModel->id,
            'stop_id'   => $request->integer('stop_id'),
            'stop_type' => $request->input('stop_type'),
            'priority'  => max(0, $minPriority - 1),
        ]);

        $stop->load('stop');

        return response()->json([
            'ok'   => true,
            'stop' => $this->formatStop($stop),
        ], 201);
    }

    public function update(UpdateRouteStopRequest $request, int $route, int $stop): JsonResponse
    {
        $routeModel = Route::findOrFail($route);
        $stopModel  = RouteStop::where('route_id', $routeModel->id)->findOrFail($stop);

        $stopModel->update([
            'stop_id'   => $request->integer('stop_id'),
            'stop_type' => $request->input('stop_type'),
        ]);

        $stopModel->load('stop');

        return response()->json([
            'ok'   => true,
            'stop' => $this->formatStop($stopModel),
        ]);
    }

    public function destroy(int $route, int $stop): JsonResponse
    {
        $routeModel = Route::findOrFail($route);
        RouteStop::where('route_id', $routeModel->id)->findOrFail($stop)->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Reorder stops using a single CASE update.
     * Verifies all IDs belong to the route to prevent cross-route writes.
     */
    public function reorder(Request $request, int $route): JsonResponse
    {
        $routeModel = Route::findOrFail($route);
        $ids        = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return response()->json(['ok' => false, 'message' => 'No IDs provided.'], 422);
        }

        // Validate all IDs belong to this route
        $validCount = DB::table('route_stops')
            ->where('route_id', $routeModel->id)
            ->whereIn('id', $ids)
            ->count();

        if ($validCount !== count($ids)) {
            return response()->json(['ok' => false, 'message' => 'Invalid stop IDs.'], 403);
        }

        // Build single CASE update — priority desc: first item gets highest number
        $total = count($ids);
        $cases = [];
        foreach ($ids as $index => $id) {
            $priority = $total - $index;
            $cases[]  = "WHEN {$id} THEN {$priority}";
        }
        $casesSql = implode(' ', $cases);

        DB::table('route_stops')
            ->where('route_id', $routeModel->id)
            ->whereIn('id', $ids)
            ->update(['priority' => DB::raw("CASE id {$casesSql} END")]);

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------

    private function formatStop(RouteStop $stop): array
    {
        $stopTypeLabels = [
            'pickup'  => 'Đón',
            'dropoff' => 'Trả',
            'both'    => 'Đón và trả',
        ];

        return [
            'id'              => $stop->id,
            'stop_id'         => $stop->stop_id,
            'stop_name'       => $stop->stop?->name ?? '',
            'stop_type'       => $stop->stop_type,
            'stop_type_label' => $stopTypeLabels[$stop->stop_type] ?? $stop->stop_type,
            'priority'        => $stop->priority,
        ];
    }
}
