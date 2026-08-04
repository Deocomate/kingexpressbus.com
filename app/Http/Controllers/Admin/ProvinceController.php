<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProvinceRequest;
use App\Http\Requests\Admin\UpdateProvinceRequest;
use App\Models\Province;
use App\Support\Admin\DeleteBlockedException;
use App\Support\Admin\DeleteGuard;
use App\Support\Admin\UploadStager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProvinceController extends Controller
{
    public function create(): View
    {
        return view('admin.locations.form-province', [
            'province' => null,
        ]);
    }

    public function store(StoreProvinceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['slug']);
        $data['thumbnail_url'] = $this->commitSingleUpload($data['thumbnail_url'] ?? null, 'provinces/thumbnails');
        $data['image_list_url'] = $this->commitAlbumUpload($data['image_list_url'] ?? [], 'provinces/albums');

        Province::create($data);

        return redirect()->route('admin.locations.index', ['section' => 'provinces'])
            ->with('success', 'Đã thêm tỉnh/thành phố.');
    }

    public function edit(Province $province): View
    {
        return view('admin.locations.form-province', [
            'province' => $province,
        ]);
    }

    public function update(UpdateProvinceRequest $request, Province $province): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['slug']);
        $data['thumbnail_url'] = $this->commitSingleUpload($data['thumbnail_url'] ?? null, 'provinces/thumbnails', $province->thumbnail_url);
        $data['image_list_url'] = $this->commitAlbumUpload($data['image_list_url'] ?? [], 'provinces/albums', $province->image_list_url ?? []);

        $province->update($data);

        return redirect()->route('admin.locations.index', ['section' => 'provinces'])
            ->with('success', 'Đã cập nhật tỉnh/thành phố.');
    }

    public function destroy(Province $province): RedirectResponse
    {
        try {
            DeleteGuard::assertNoBookings(DeleteGuard::provinceBookingCount((int) $province->id), 'tỉnh/thành');
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        $province->delete();

        return redirect()->route('admin.locations.index', ['section' => 'provinces'])
            ->with('success', 'Đã xóa tỉnh/thành phố.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return back()->with('error', 'Chưa chọn bản ghi nào.');
        }

        try {
            $total = array_sum(array_map(
                fn (int $id) => DeleteGuard::provinceBookingCount($id),
                $ids,
            ));
            DeleteGuard::assertNoBookings($total, count($ids) . ' tỉnh/thành đã chọn');
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        Province::whereIn('id', $ids)->delete();

        return redirect()->route('admin.locations.index', ['section' => 'provinces'])
            ->with('success', 'Đã xóa ' . count($ids) . ' tỉnh/thành phố.');
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
                Province::where('id', $id)->update(['priority' => $total - $index]);
            }
        });

        return back()->with('success', 'Đã lưu thứ tự.');
    }

    // ─── Upload helpers ───────────────────────────────────────────────────────

    private function commitSingleUpload(?string $value, string $dir, ?string $existing = null): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Existing path returned from FilePond (contains '/') → keep as-is
        if (str_contains($value, '/')) {
            return $value;
        }

        // Token → commit staged file
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
