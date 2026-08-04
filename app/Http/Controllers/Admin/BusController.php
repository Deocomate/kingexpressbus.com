<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBusRequest;
use App\Http\Requests\Admin\UpdateBusRequest;
use App\Models\Bus;
use App\Models\BusService;
use App\Support\Admin\DeleteBlockedException;
use App\Support\Admin\DeleteGuard;
use App\Support\Admin\HtmlSanitizer;
use App\Support\Admin\TableQuery;
use App\Support\Admin\Tables\BusServiceTableConfig;
use App\Support\Admin\Tables\BusTableConfig;
use App\Support\Admin\UploadStager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusController extends Controller
{
    public function index(Request $request): View
    {
        $section = $request->input('section', 'buses');

        if ($section === 'services') {
            return $this->indexServices($request);
        }

        $config = BusTableConfig::make();
        $base   = BusTableConfig::baseQuery();

        $tq = TableQuery::make($base, $config)->process($request);

        // Eager load services for all buses in current page
        $busIds   = $tq->paginator()->pluck('id')->all();
        $services = Bus::whereIn('id', $busIds)->with('services')->get()->keyBy('id');

        $allServices = BusService::orderBy('name')->get(['id', 'name', 'icon']);

        return view('admin.buses.index', [
            'section'     => 'buses',
            'paginator'   => $tq->paginator(),
            'tableConfig' => $config,
            'activeSearch'=> $tq->activeSearch(),
            'activeSortKey' => $tq->activeSortKey(),
            'activeSortDir' => $tq->activeSortDir(),
            'busServices'   => $services,
            'allServices'   => $allServices,
        ]);
    }

    private function indexServices(Request $request): View
    {
        $config = BusServiceTableConfig::make();
        $base   = BusServiceTableConfig::baseQuery();

        $tq = TableQuery::make($base, $config)->process($request);

        return view('admin.buses.index', [
            'section'         => 'services',
            'servicesPaginator' => $tq->paginator(),
            'servicesConfig'    => $config,
            'activeSearch'      => $tq->activeSearch(),
        ]);
    }

    public function create(): View
    {
        $allServices = BusService::orderBy('name')->get(['id', 'name']);

        return view('admin.buses.form', [
            'bus'         => null,
            'isEdit'      => false,
            'allServices' => $allServices,
        ]);
    }

    public function store(StoreBusRequest $request): RedirectResponse
    {
        $data     = $this->prepareData($request);
        $services = $request->input('services', []);

        $bus = Bus::create($data);
        $bus->services()->sync($services);

        return redirect()->route('admin.buses.edit', $bus->id)
            ->with('success', 'Đã tạo xe thành công.');
    }

    public function edit(int $bus): View
    {
        $busModel    = Bus::with('services')->findOrFail($bus);
        $allServices = BusService::orderBy('name')->get(['id', 'name']);

        return view('admin.buses.form', [
            'bus'         => $busModel,
            'isEdit'      => true,
            'allServices' => $allServices,
        ]);
    }

    public function update(UpdateBusRequest $request, int $bus): RedirectResponse
    {
        $busModel = Bus::findOrFail($bus);

        $data     = $this->prepareData($request, $busModel);
        $services = $request->input('services', []);

        $busModel->update($data);
        $busModel->services()->sync($services);

        return redirect()->route('admin.buses.edit', $bus)
            ->with('success', 'Đã cập nhật xe thành công.');
    }

    public function destroy(int $bus): RedirectResponse
    {
        $busModel = Bus::findOrFail($bus);

        try {
            DeleteGuard::assertNoBookings(
                DeleteGuard::busBookingCount($busModel->id),
                "xe \"{$busModel->name}\""
            );
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->deleteBusFiles($busModel);
        $busModel->delete();

        return redirect()->route('admin.buses.index')
            ->with('success', 'Đã xóa xe.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return back()->with('error', 'Không có bản ghi nào được chọn.');
        }

        $buses   = Bus::whereIn('id', $ids)->get();
        $blocked = [];
        $deleted = 0;

        foreach ($buses as $busModel) {
            $count = DeleteGuard::busBookingCount($busModel->id);
            if ($count > 0) {
                $blocked[] = "\"{$busModel->name}\" ({$count} đơn)";
                continue;
            }
            $this->deleteBusFiles($busModel);
            $busModel->delete();
            $deleted++;
        }

        $message = "Đã xóa {$deleted} xe.";
        if (!empty($blocked)) {
            $message .= ' Không thể xóa: ' . implode(', ', $blocked) . '.';
        }

        $flashKey = empty($blocked) ? 'success' : 'error';
        return back()->with($flashKey, $message);
    }

    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return response()->json(['ok' => false], 422);
        }

        $total = count($ids);
        $cases = [];
        foreach ($ids as $index => $id) {
            $priority = $total - $index;
            $cases[]  = "WHEN {$id} THEN {$priority}";
        }
        $casesSql = implode(' ', $cases);

        DB::table('buses')
            ->whereIn('id', $ids)
            ->update(['priority' => DB::raw("CASE id {$casesSql} END")]);

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------

    private function prepareData(Request $request, ?Bus $existing = null): array
    {
        $validated = $request->validated();
        $session   = session()->getId();

        // Commit thumbnail
        $thumbnailUrl = $existing?->thumbnail_url;
        if (!empty($validated['thumbnail_url'])) {
            $token = $validated['thumbnail_url'];
            if (str_contains($token, '~')) {
                if ($thumbnailUrl) {
                    UploadStager::delete($thumbnailUrl);
                }
                $thumbnailUrl = UploadStager::commit($token, 'buses/thumbnails', $session);
            } else {
                $thumbnailUrl = $token;
            }
        } elseif (array_key_exists('thumbnail_url', $validated) && empty($validated['thumbnail_url'])) {
            if ($thumbnailUrl) {
                UploadStager::delete($thumbnailUrl);
            }
            $thumbnailUrl = null;
        }

        // Commit album images (max 10 enforced by validation)
        $existingAlbum  = $existing?->image_list_url ?? [];
        $incomingTokens = $validated['image_list_url'] ?? [];
        $newAlbum       = [];
        foreach ($incomingTokens as $token) {
            if (str_contains($token, '~')) {
                $newAlbum[] = UploadStager::commit($token, 'buses/albums', $session);
            } else {
                $newAlbum[] = $token;
            }
        }

        foreach ($existingAlbum as $oldPath) {
            if (!in_array($oldPath, $newAlbum)) {
                UploadStager::delete($oldPath);
            }
        }

        $content = isset($validated['content'])
            ? HtmlSanitizer::clean($validated['content'])
            : ($existing?->content ?? '');

        return [
            'name'           => $validated['name'],
            'model_name'     => $validated['model_name'] ?? null,
            'seat_count'     => (int) $validated['seat_count'],
            'priority'       => $validated['priority'] ?? 0,
            'thumbnail_url'  => $thumbnailUrl,
            'image_list_url' => empty($newAlbum) ? null : $newAlbum,
            'content'        => $content,
        ];
    }

    private function deleteBusFiles(Bus $bus): void
    {
        if ($bus->thumbnail_url) {
            UploadStager::delete($bus->thumbnail_url);
        }
        foreach ($bus->image_list_url ?? [] as $path) {
            UploadStager::delete($path);
        }
    }
}
