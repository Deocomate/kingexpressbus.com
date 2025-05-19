<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// <<< Thêm
use Illuminate\Support\Str;

// <<< Thêm
use Illuminate\Validation\Rule;

// <<< Thêm

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy danh sách routes và join với bảng provinces để lấy tên tỉnh đi/đến
        $routes = DB::table('routes')
            ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
            ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
            ->select(
                'routes.*',
                'p_start.name as province_start_name',
                'p_end.name as province_end_name'
            )
            ->orderBy('routes.priority', 'asc')
            ->orderBy('routes.id', 'desc') // Sắp xếp thứ cấp theo ID giảm dần (mới nhất lên đầu)
            ->get();

        return view('kingexpressbus.admin.modules.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lấy danh sách tỉnh cho dropdown
        $provinces = DB::table('provinces')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $route = null; // Không có route khi tạo mới

        return view('kingexpressbus.admin.modules.routes.createOrEdit', compact('provinces', 'route'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'province_id_start' => 'required|exists:provinces,id|different:province_id_end', // Điểm đi phải khác điểm đến
            'province_id_end' => 'required|exists:provinces,id',
            'title' => 'required|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|string|max:255',
            'images' => 'required|array',
            'distance' => 'required|integer|min:0',
            'duration' => 'required|string|max:255',
            'start_price' => 'required|integer|min:0',
            'detail' => 'required|string',
            'priority' => 'required|integer',
            'slug' => [
                'nullable',
                'max:255',
                Rule::unique('routes') // Slug phải là duy nhất trong bảng routes
            ],
        ]);

        // Tự động tạo slug từ title nếu rỗng
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Kiểm tra và đảm bảo slug là duy nhất
            $count = DB::table('routes')->where('slug', $validated['slug'])->count();
            if ($count > 0) {
                $validated['slug'] .= '-' . time();
            }
        }

        // Xử lý JSON encode cho images (lọc giá trị rỗng)
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = array_filter($validated['images'], fn($value) => $value !== null && $value !== '');
            $validated['images'] = !empty($validated['images']) ? json_encode(array_values($validated['images'])) : null;
        } else {
            $validated['images'] = null;
        }

        DB::table('routes')->insert($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Tuyến đường đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $route = DB::table('routes')->find($id);
        if (!$route) {
            abort(404);
        }

        // Decode images
        try {
            $route->images = json_decode($route->images, true);
            if (!is_array($route->images)) $route->images = [];
        } catch (\Exception $e) {
            $route->images = [];
        }

        $provinces = DB::table('provinces')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        return view('kingexpressbus.admin.modules.routes.createOrEdit', compact('provinces', 'route'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $route = DB::table('routes')->find($id);
        if (!$route) {
            abort(404);
        }

        $validated = $request->validate([
            'province_id_start' => 'required|exists:provinces,id|different:province_id_end',
            'province_id_end' => 'required|exists:provinces,id',
            'title' => 'required|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|string|max:255',
            'images' => 'required|array',
            'distance' => 'required|integer|min:0',
            'duration' => 'required|string|max:255',
            'start_price' => 'required|integer|min:0',
            'detail' => 'required|string',
            'priority' => 'required|integer',
            'slug' => [
                'nullable',
                'max:255',
                Rule::unique('routes')->ignore($id) // Bỏ qua ID hiện tại khi kiểm tra unique
            ],
        ]);

        // Tự động tạo slug từ title nếu rỗng
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Kiểm tra và đảm bảo slug là duy nhất (trừ chính nó)
            $count = DB::table('routes')->where('slug', $validated['slug'])->where('id', '!=', $id)->count();
            if ($count > 0) {
                $validated['slug'] .= '-' . time();
            }
        }

        // Xử lý JSON encode cho images (lọc giá trị rỗng)
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = array_filter($validated['images'], fn($value) => $value !== null && $value !== '');
            $validated['images'] = !empty($validated['images']) ? json_encode(array_values($validated['images'])) : null;
        } else {
            $validated['images'] = null;
        }

        DB::table('routes')->where('id', $id)->update($validated);

        return redirect()->route('admin.routes.index')->with('success', 'Tuyến đường đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $route = DB::table('routes')->find($id);
        if (!$route) {
            return back()->with('error', 'Không tìm thấy Tuyến đường để xóa.');
        }

        DB::table('routes')->where('id', $id)->delete();

        return redirect()->route('admin.routes.index')->with('success', 'Tuyến đường đã được xóa thành công!');
    }
}
