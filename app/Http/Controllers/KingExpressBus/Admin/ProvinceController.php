<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Thêm dòng này
use Illuminate\Support\Str;

// Thêm dòng này nếu cần tự động tạo slug

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $provinces = DB::table('provinces') // [cite: 265]
        ->orderBy('priority', 'asc') // Sắp xếp theo ưu tiên
        ->orderBy('name', 'asc') // Rồi theo tên
        ->get();
        // Sử dụng đường dẫn view tương ứng với module provinces [cite: 93, 100]
        return view('kingexpressbus.admin.modules.provinces.index', compact('provinces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $province = null;
        // Đường dẫn view createOrEdit [cite: 101, 102]
        return view('kingexpressbus.admin.modules.provinces.createOrEdit', compact('province'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validation rules dựa trên cấu trúc bảng 'provinces' [cite: 265, 266]
            'name' => 'required|unique:provinces|max:255',
            'type' => 'required|in:thanhpho,tinh', // [cite: 265]
            'title' => 'nullable|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|max:255', // [cite: 265]
            'images' => 'nullable|array', // [cite: 266] Kiểm tra là mảng
            'detail' => 'nullable|string', // [cite: 266]
            'priority' => 'nullable|integer', // [cite: 266]
            'slug' => 'nullable|unique:provinces|max:255', // [cite: 266]
        ]);

        // Tự động tạo slug nếu không được cung cấp
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Xử lý JSON encode cho images nếu nó là mảng [cite: 266]
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = json_encode($validated['images']);
        } else {
            $validated['images'] = null; // Đảm bảo là null nếu không có hoặc không phải mảng
        }

        // Gán giá trị mặc định cho priority nếu không có [cite: 266]
        $validated['priority'] = $validated['priority'] ?? 0;

        DB::table('provinces')->insert($validated); // [cite: 104]

        // Sử dụng tên route resource [cite: 104]
        return redirect()->route('admin.provinces.index')->with('success', 'Tỉnh/Thành phố đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Thường không cần thiết cho trang admin CRUD cơ bản [cite: 105]
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $province = DB::table('provinces')->where('id', $id)->first(); // [cite: 106]
        if (!$province) { // [cite: 107]
            abort(404); // [cite: 108]
        }

        // Decode JSON images để hiển thị trong form [cite: 266]
        // Cần kiểm tra $province->images có phải là JSON hợp lệ không trước khi decode
        try {
            $province->images = json_decode($province->images, true);
            // Đảm bảo $province->images là mảng nếu decode thành công hoặc null/trống
            if (!is_array($province->images)) {
                $province->images = [];
            }
        } catch (\Exception $e) {
            $province->images = []; // Gán mảng rỗng nếu có lỗi decode
        }


        // Đường dẫn view createOrEdit [cite: 109]
        return view('kingexpressbus.admin.modules.provinces.createOrEdit', compact('province'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Kiểm tra xem tỉnh/thành phố có tồn tại không
        $provinceExists = DB::table('provinces')->where('id', $id)->exists();
        if (!$provinceExists) {
            abort(404);
        }

        $validated = $request->validate([
            // Unique rule cần bỏ qua id hiện tại [cite: 110]
            'name' => 'required|unique:provinces,name,' . $id . '|max:255',
            'type' => 'required|in:thanhpho,tinh', // [cite: 265]
            'title' => 'nullable|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|max:255', // [cite: 265]
            'images' => 'nullable|array', // [cite: 266] Kiểm tra là mảng
            'detail' => 'nullable|string', // [cite: 266]
            'priority' => 'nullable|integer', // [cite: 266]
            'slug' => 'nullable|unique:provinces,slug,' . $id . '|max:255', // [cite: 266]
        ]);

        // Tự động tạo slug nếu không được cung cấp
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Xử lý JSON encode cho images nếu nó là mảng [cite: 266]
        if (isset($validated['images']) && is_array($validated['images'])) {
            // Loại bỏ các giá trị null hoặc rỗng trong mảng trước khi encode
            $validated['images'] = array_filter($validated['images'], function ($value) {
                return $value !== null && $value !== '';
            });
            $validated['images'] = json_encode(array_values($validated['images'])); // Re-index array
        } else {
            $validated['images'] = null; // Đảm bảo là null nếu không có hoặc không phải mảng
        }


        // Gán giá trị mặc định cho priority nếu không có [cite: 266]
        $validated['priority'] = $validated['priority'] ?? 0;

        DB::table('provinces')->where('id', $id)->update($validated); // [cite: 111]

        return redirect()->route('admin.provinces.index')->with('success', 'Tỉnh/Thành phố đã được cập nhật thành công!'); // [cite: 111]
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Kiểm tra xem tỉnh/thành phố có tồn tại không trước khi xóa
        $provinceExists = DB::table('provinces')->where('id', $id)->exists();
        if (!$provinceExists) {
            return redirect()->route('admin.provinces.index')->with('error', 'Không tìm thấy Tỉnh/Thành phố để xóa.');
        }

        DB::table('provinces')->where('id', $id)->delete(); // [cite: 112]
        return redirect()->route('admin.provinces.index')->with('success', 'Tỉnh/Thành phố đã được xóa thành công!'); // [cite: 113]
    }
}
