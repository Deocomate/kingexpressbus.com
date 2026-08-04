<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDistrictRequest;
use App\Http\Requests\Admin\UpdateDistrictRequest;
use App\Models\District;
use App\Support\Admin\DeleteBlockedException;
use App\Support\Admin\DeleteGuard;
use App\Support\Admin\OptionSources\LocationOptionSource;
use App\Support\Admin\UploadStager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DistrictController extends Controller
{
    public function create(): View
    {
        return view('admin.locations.form-district', [
            'district'      => null,
            'districtTypes' => LocationOptionSource::districtTypeOptions(),
        ]);
    }

    public function store(StoreDistrictRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['slug']);
        $data['thumbnail_url'] = $this->commitSingleUpload($data['thumbnail_url'] ?? null, 'districts/thumbnails');
        $data['image_list_url'] = $this->commitAlbumUpload($data['image_list_url'] ?? [], 'districts/albums');

        District::create($data);

        return redirect()->route('admin.locations.index', ['section' => 'districts'])
            ->with('success', 'Đã thêm địa điểm.');
    }

    public function edit(District $district): View
    {
        return view('admin.locations.form-district', [
            'district'      => $district->load(['province', 'districtType']),
            'districtTypes' => LocationOptionSource::districtTypeOptions(),
        ]);
    }

    public function update(UpdateDistrictRequest $request, District $district): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['slug']);
        $data['thumbnail_url'] = $this->commitSingleUpload($data['thumbnail_url'] ?? null, 'districts/thumbnails', $district->thumbnail_url);
        $data['image_list_url'] = $this->commitAlbumUpload($data['image_list_url'] ?? [], 'districts/albums', $district->image_list_url ?? []);

        $district->update($data);

        return redirect()->route('admin.locations.index', ['section' => 'districts'])
            ->with('success', 'Đã cập nhật địa điểm.');
    }

    public function destroy(District $district): RedirectResponse
    {
        try {
            DeleteGuard::assertNoBookings(DeleteGuard::districtBookingCount((int) $district->id), 'địa điểm');
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        $district->delete();

        return redirect()->route('admin.locations.index', ['section' => 'districts'])
            ->with('success', 'Đã xóa địa điểm.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return back()->with('error', 'Chưa chọn bản ghi nào.');
        }

        try {
            $total = array_sum(array_map(
                fn (int $id) => DeleteGuard::districtBookingCount($id),
                $ids,
            ));
            DeleteGuard::assertNoBookings($total, count($ids) . ' địa điểm đã chọn');
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        District::whereIn('id', $ids)->delete();

        return redirect()->route('admin.locations.index', ['section' => 'districts'])
            ->with('success', 'Đã xóa ' . count($ids) . ' địa điểm.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return response()->json(['ok' => false, 'message' => 'Không có dữ liệu để sắp xếp.'], 422);
        }

        $total = count($ids);
        DB::transaction(function () use ($ids, $total) {
            foreach ($ids as $index => $id) {
                District::where('id', $id)->update(['priority' => $total - $index]);
            }
        });

        return response()->json(['ok' => true]);
    }

    // ─── Upload helpers ───────────────────────────────────────────────────────

    private function commitSingleUpload(?string $value, string $dir, ?string $existing = null): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_contains($value, '/')) {
            return $value;
        }

        return UploadStager::commit($value, $dir, session()->getId());
    }

    private function commitAlbumUpload(array $values, string $dir, array $existing = []): array
    {
        $result = [];

        foreach ($values as $value) {
            if (empty($value)) {
                continue;
            }

            if (str_contains($value, '/')) {
                $result[] = $value;
            } else {
                $result[] = UploadStager::commit($value, $dir, session()->getId());
            }
        }

        return $result;
    }
}
