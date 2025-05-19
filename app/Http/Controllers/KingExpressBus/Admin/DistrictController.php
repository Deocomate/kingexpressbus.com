<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// <<< Thêm
use Illuminate\Validation\Rule;

// <<< Thêm

class DistrictController extends Controller
{
    // --- index method giữ nguyên ---
    public function index(Request $request)
    {
        $provinces = DB::table('provinces')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $selectedProvinceId = $request->query('province_id');
        $districts = collect();

        if ($selectedProvinceId) {
            $districts = DB::table('districts')
                ->where('province_id', $selectedProvinceId)
                ->orderBy('priority', 'asc')
                ->orderBy('name', 'asc')
                ->get();
        }

        return view('kingexpressbus.admin.modules.districts.index', compact('provinces', 'selectedProvinceId', 'districts'));
    }

    // --- create method giữ nguyên ---
    public function create(Request $request)
    {
        $selectedProvinceId = $request->query('province_id');

        if (!$selectedProvinceId) {
            return redirect()->route('admin.districts.index')->with('error', 'Vui lòng chọn Tỉnh/Thành phố trước khi tạo Quận/Huyện.');
        }

        $provinces = DB::table('provinces')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $district = null;

        return view('kingexpressbus.admin.modules.districts.createOrEdit', compact('provinces', 'selectedProvinceId', 'district'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'name' => [
                'required',
                'max:255',
                Rule::unique('districts')->where(function ($query) use ($request) {
                    return $query->where('province_id', $request->input('province_id'));
                }),
            ],
            'type' => 'required|in:quan,huyen,thixa,thanhpho',
            'slug' => [ // Slug không bắt buộc nhập
                'nullable',
                'max:255',
                Rule::unique('districts')->where(function ($query) use ($request) {
                    return $query->where('province_id', $request->input('province_id'));
                }),
            ],
            'title' => 'required|max:255',         // <<< Bắt buộc
            'description' => 'required|string',    // <<< Bắt buộc
            'thumbnail' => 'required|string|max:255', // <<< Bắt buộc (giả sử là URL)
            'images' => 'required|array',          // <<< Bắt buộc (phải là array)
            'detail' => 'required|string',         // <<< Bắt buộc
            'priority' => 'required|integer',      // <<< Bắt buộc
        ]);

        // Tự động tạo slug nếu không được cung cấp
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Kiểm tra lại slug sau khi tạo tự động để đảm bảo unique (trường hợp tên giống nhau)
            $count = DB::table('districts')
                ->where('province_id', $validated['province_id'])
                ->where('slug', $validated['slug'])
                ->count();
            if ($count > 0) {
                $validated['slug'] .= '-' . time(); // Thêm timestamp để tránh trùng
            }
        }

        // Xử lý JSON encode cho images (lọc giá trị rỗng)
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = array_filter($validated['images'], function ($value) {
                return $value !== null && $value !== '';
            });
            // Chỉ encode nếu mảng không rỗng sau khi lọc
            $validated['images'] = !empty($validated['images']) ? json_encode(array_values($validated['images'])) : null;
        } else {
            $validated['images'] = null; // Đảm bảo là null nếu không phải mảng
        }


        DB::table('districts')->insert($validated);

        return redirect()->route('admin.districts.index', ['province_id' => $validated['province_id']])
            ->with('success', 'Quận/Huyện đã được tạo thành công!');
    }

    // --- show method giữ nguyên ---
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $district = DB::table('districts')->find($id);
        if (!$district) {
            abort(404);
        }

        // Decode JSON images để hiển thị trong form
        try {
            $district->images = json_decode($district->images, true);
            if (!is_array($district->images)) {
                $district->images = []; // Đảm bảo là mảng nếu decode lỗi hoặc null
            }
        } catch (\Exception $e) {
            $district->images = []; // Gán mảng rỗng nếu có lỗi decode
        }

        $provinces = DB::table('provinces')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $selectedProvinceId = $district->province_id;

        return view('kingexpressbus.admin.modules.districts.createOrEdit', compact('provinces', 'selectedProvinceId', 'district'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $district = DB::table('districts')->find($id);
        if (!$district) {
            abort(404);
        }

        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'name' => [
                'required',
                'max:255',
                Rule::unique('districts')->where(function ($query) use ($request) {
                    return $query->where('province_id', $request->input('province_id'));
                })->ignore($id),
            ],
            'type' => 'required|in:quan,huyen,thixa,thanhpho',
            'slug' => [ // Slug không bắt buộc nhập
                'nullable',
                'max:255',
                Rule::unique('districts')->where(function ($query) use ($request) {
                    return $query->where('province_id', $request->input('province_id'));
                })->ignore($id),
            ],
            'title' => 'required|max:255',         // <<< Bắt buộc
            'description' => 'required|string',    // <<< Bắt buộc
            'thumbnail' => 'required|string|max:255', // <<< Bắt buộc
            'images' => 'required|array',          // <<< Bắt buộc
            'detail' => 'required|string',         // <<< Bắt buộc
            'priority' => 'required|integer',      // <<< Bắt buộc
        ]);

        // Tự động tạo slug nếu không được cung cấp
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Kiểm tra lại slug sau khi tạo tự động để đảm bảo unique (trừ chính nó)
            $count = DB::table('districts')
                ->where('province_id', $validated['province_id'])
                ->where('slug', $validated['slug'])
                ->where('id', '!=', $id) // Loại trừ bản ghi hiện tại
                ->count();
            if ($count > 0) {
                $validated['slug'] .= '-' . time();
            }
        }

        // Xử lý JSON encode cho images (lọc giá trị rỗng)
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = array_filter($validated['images'], function ($value) {
                return $value !== null && $value !== '';
            });
            $validated['images'] = !empty($validated['images']) ? json_encode(array_values($validated['images'])) : null;
        } else {
            $validated['images'] = null;
        }

        DB::table('districts')->where('id', $id)->update($validated);

        return redirect()->route('admin.districts.index', ['province_id' => $validated['province_id']])
            ->with('success', 'Quận/Huyện đã được cập nhật thành công!');
    }

    // --- destroy method giữ nguyên ---
    public function destroy(string $id)
    {
        $district = DB::table('districts')->find($id);
        if (!$district) {
            return back()->with('error', 'Không tìm thấy Quận/Huyện để xóa.');
        }

        $provinceId = $district->province_id;

        DB::table('districts')->where('id', $id)->delete();

        return redirect()->route('admin.districts.index', ['province_id' => $provinceId])
            ->with('success', 'Quận/Huyện đã được xóa thành công!');
    }
}
