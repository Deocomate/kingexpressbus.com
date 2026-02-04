<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\RouteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    protected RouteService $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin_id' => 'required|integer',
            'origin_type' => 'required|string|in:province,district,stop',
            'destination_id' => 'required|integer',
            'destination_type' => 'required|string|in:province,district,stop',
            'departure_date' => 'required|date_format:d/m/Y',
            'return_date' => 'nullable|date_format:d/m/Y',
        ]);

        try {
            $startProvinceId = $this->routeService->resolveProvinceId(
                $validated['origin_type'],
                (int) $validated['origin_id']
            );
            $endProvinceId = $this->routeService->resolveProvinceId(
                $validated['destination_type'],
                (int) $validated['destination_id']
            );

            if (!$startProvinceId || !$endProvinceId) {
                return $this->searchErrorResponse($request, __('client.route_show.search.invalid_location'));
            }

            $route = $this->routeService->findRouteByProvinces($startProvinceId, $endProvinceId);

            if (!$route) {
                return $this->searchErrorResponse($request, __('client.route_show.search.no_route_found'));
            }

            $params = [
                'slug' => $route->slug,
                'departure_date' => $validated['departure_date'],
                'origin_id' => $validated['origin_id'],
                'origin_type' => $validated['origin_type'],
                'destination_id' => $validated['destination_id'],
                'destination_type' => $validated['destination_type'],
            ];

            if (!empty($validated['return_date'])) {
                $params['return_date'] = $validated['return_date'];
            }

            $redirectUrl = route('client.routes.show', $params);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'redirect_url' => $redirectUrl]);
            }

            return redirect()->to($redirectUrl);
        } catch (\Throwable $exception) {
            Log::error('Client route search failed', ['error' => $exception->getMessage()]);
            return $this->searchErrorResponse($request, __('client.route_show.search.system_error'));
        }
    }

    private function searchErrorResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }

        return redirect()->back()->withInput($request->all())->with('error', $message);
    }
}
