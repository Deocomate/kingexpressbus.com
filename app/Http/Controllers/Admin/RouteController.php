<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRouteRequest;
use App\Http\Requests\Admin\UpdateRouteRequest;
use App\Models\Route;
use App\Support\Admin\AdminResponse;
use App\Support\Admin\DeleteBlockedException;
use App\Support\Admin\DeleteGuard;
use App\Support\Admin\TableQuery;
use App\Support\Admin\Tables\RouteTableConfig;
use App\Support\Admin\UploadStager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RouteController extends Controller
{
    public function index(Request $request): View
    {
        $config = RouteTableConfig::make();
        $base = RouteTableConfig::baseQuery();

        // Province filters
        if ($startId = $request->integer('filter_province_start')) {
            $base->where('r.province_start_id', $startId);
        }
        if ($endId = $request->integer('filter_province_end')) {
            $base->where('r.province_end_id', $endId);
        }

        $tq = TableQuery::make($base, $config)->process($request);

        $provinces = DB::table('provinces')->orderBy('name')->get(['id', 'name']);

        if ($request->header('X-Partial') === 'table') {
            return view('admin.routes._table-rows', [
                'paginator' => $tq->paginator(),
                'activeSortKey' => $tq->activeSortKey(),
                'activeSortDir' => $tq->activeSortDir(),
            ]);
        }

        return view('admin.routes.index', [
            'paginator' => $tq->paginator(),
            'tableConfig' => $config,
            'activeSearch' => $tq->activeSearch(),
            'activeSortKey' => $tq->activeSortKey(),
            'activeSortDir' => $tq->activeSortDir(),
            'filterProvinceStart' => $request->integer('filter_province_start'),
            'filterProvinceEnd' => $request->integer('filter_province_end'),
            'provinces' => $provinces,
        ]);
    }

    public function create(): View
    {
        return view('admin.routes.form', [
            'route' => null,
            'isEdit' => false,
        ]);
    }

    public function store(StoreRouteRequest $request): RedirectResponse
    {
        $data = $this->prepareData($request);

        $route = Route::create($data);

        return redirect()->route('admin.routes.edit', $route->id)
            ->with('success', 'Đã tạo tuyến đường thành công.');
    }

    public function edit(int $route): View
    {
        $routeModel = Route::with(['startProvince', 'endProvince', 'routeStops.stop'])->findOrFail($route);

        return view('admin.routes.form', [
            'route' => $routeModel,
            'isEdit' => true,
        ]);
    }

    public function update(UpdateRouteRequest $request, int $route): RedirectResponse
    {
        $routeModel = Route::findOrFail($route);

        $data = $this->prepareData($request, $routeModel);

        $routeModel->update($data);

        return redirect()->route('admin.routes.edit', $route)
            ->with('success', 'Đã cập nhật tuyến đường thành công.');
    }

    public function destroy(int $route): RedirectResponse
    {
        $routeModel = Route::findOrFail($route);

        try {
            DeleteGuard::assertNoBookings(
                DeleteGuard::routeBookingCount($routeModel->id),
                "tuyến \"{$routeModel->name}\""
            );
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->deleteRouteFiles($routeModel);
        $routeModel->delete();

        return redirect()->route('admin.routes.index')
            ->with('success', 'Đã xóa tuyến đường.');
    }

    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return AdminResponse::bulkResult($request, false, 'Không có bản ghi nào được chọn.');
        }

        $routes = Route::whereIn('id', $ids)->get();
        $blocked = [];
        $deleted = 0;

        foreach ($routes as $routeModel) {
            $count = DeleteGuard::routeBookingCount($routeModel->id);
            if ($count > 0) {
                $blocked[] = "\"{$routeModel->name}\" ({$count} đơn)";

                continue;
            }

            $this->deleteRouteFiles($routeModel);
            $routeModel->delete();
            $deleted++;
        }

        $message = "Đã xóa {$deleted} tuyến.";
        if (! empty($blocked)) {
            $message .= ' Không thể xóa: '.implode(', ', $blocked).'.';
        }

        return AdminResponse::bulkResult($request, empty($blocked), $message);
    }

    public function reorder(Request $request): JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return response()->json(['ok' => false], 422);
        }

        $total = count($ids);
        $cases = [];
        foreach ($ids as $index => $id) {
            $priority = $total - $index;
            $cases[] = "WHEN {$id} THEN {$priority}";
        }
        $casesSql = implode(' ', $cases);

        DB::table('routes')
            ->whereIn('id', $ids)
            ->update(['priority' => DB::raw("CASE id {$casesSql} END")]);

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------

    private function prepareData(Request $request, ?Route $existing = null): array
    {
        $validated = $request->validated();
        $session = session()->getId();

        // Commit thumbnail
        $thumbnailUrl = $existing?->thumbnail_url;
        if (! empty($validated['thumbnail_url'])) {
            $token = $validated['thumbnail_url'];
            if (str_contains($token, '~')) {
                // Delete old thumbnail if replacing
                if ($thumbnailUrl) {
                    UploadStager::delete($thumbnailUrl);
                }
                $thumbnailUrl = UploadStager::commit($token, 'routes/thumbnails', $session);
            } else {
                $thumbnailUrl = $token; // already committed path
            }
        } elseif (array_key_exists('thumbnail_url', $validated) && empty($validated['thumbnail_url'])) {
            if ($thumbnailUrl) {
                UploadStager::delete($thumbnailUrl);
            }
            $thumbnailUrl = null;
        }

        // Commit album images
        $existingAlbum = $existing?->image_list_url ?? [];
        $incomingTokens = $validated['image_list_url'] ?? [];
        $newAlbum = [];
        foreach ($incomingTokens as $token) {
            if (str_contains($token, '~')) {
                $newAlbum[] = UploadStager::commit($token, 'routes/albums', $session);
            } else {
                $newAlbum[] = $token; // kept existing path
            }
        }

        // Delete removed album files
        foreach ($existingAlbum as $oldPath) {
            if (! in_array($oldPath, $newAlbum)) {
                UploadStager::delete($oldPath);
            }
        }

        // Persist raw editor HTML; sanitize on public render only.
        $content = $validated['content'] ?? ($existing?->content ?? '');

        return [
            'province_start_id' => $validated['province_start_id'],
            'province_end_id' => $validated['province_end_id'],
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'distance_km' => $validated['distance_km'] ?? null,
            'price_default' => $validated['price_default'] ?? null,
            'available_hotel_pickup' => (bool) ($validated['available_hotel_pickup'] ?? false),
            'priority' => $validated['priority'] ?? 0,
            'thumbnail_url' => $thumbnailUrl,
            'image_list_url' => empty($newAlbum) ? null : $newAlbum,
            'content' => $content,
        ];
    }

    private function deleteRouteFiles(Route $route): void
    {
        if ($route->thumbnail_url) {
            UploadStager::delete($route->thumbnail_url);
        }
        foreach ($route->image_list_url ?? [] as $path) {
            UploadStager::delete($path);
        }
    }
}
