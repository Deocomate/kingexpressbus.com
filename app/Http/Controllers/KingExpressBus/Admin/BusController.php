<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Thường không cần Rule::unique cho bảng này trừ khi có yêu cầu đặc biệt

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buses = DB::table('buses')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('kingexpressbus.admin.modules.buses.index', compact('buses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bus = null; // Tạo mới
        return view('kingexpressbus.admin.modules.buses.createOrEdit', compact('bus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|string|max:255',
            'images' => 'required|array',
            'name' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'type' => 'required|in:sleeper,cabin,doublecabin,limousine',
            'number_of_seats' => 'required|integer|min:1',
            'services' => 'required|array', // Dữ liệu từ text-array
            'floors' => 'required|integer|min:1',
            'seat_row_number' => 'required|integer|min:1',
            'seat_column_number' => 'required|integer|min:1',
            'detail' => 'required|string',
            'priority' => 'required|integer',
            // Không validate slug ở đây
        ]);

        // --- Slug Generation ---
        $baseSlug = Str::slug($validated['name']); // Tạo slug từ 'name'
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table('buses')->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug; // Thêm slug đã tạo vào mảng validated

        // --- JSON Handling ---
        // Images
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = array_filter($validated['images'], fn($value) => $value !== null && $value !== '');
            $validated['images'] = !empty($validated['images']) ? json_encode(array_values($validated['images'])) : null;
        } else {
            // Validation đã yêu cầu là array, nhưng để chắc chắn
            $validated['images'] = null;
        }
        // Services
        if (isset($validated['services']) && is_array($validated['services'])) {
            $validated['services'] = array_filter($validated['services'], fn($value) => $value !== null && $value !== '');
            // Encode services thành JSON nếu không rỗng
            $validated['services'] = !empty($validated['services']) ? json_encode(array_values($validated['services'])) : null;
        } else {
            $validated['services'] = null;
        }


        DB::table('buses')->insert($validated);

        return redirect()->route('admin.buses.index')->with('success', 'Loại xe đã được tạo thành công!');
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
        $bus = DB::table('buses')->find($id);
        if (!$bus) {
            abort(404);
        }

        // Decode JSON fields
        try {
            $bus->images = json_decode($bus->images, true);
            if (!is_array($bus->images)) $bus->images = [];
        } catch (\Exception $e) {
            $bus->images = [];
        }

        try {
            $bus->services = json_decode($bus->services, true);
            if (!is_array($bus->services)) $bus->services = [];
        } catch (\Exception $e) {
            $bus->services = [];
        }


        return view('kingexpressbus.admin.modules.buses.createOrEdit', compact('bus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bus = DB::table('buses')->find($id);
        if (!$bus) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|string|max:255',
            'images' => 'required|array',
            'name' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'type' => 'required|in:sleeper,cabin,doublecabin,limousine',
            'number_of_seats' => 'required|integer|min:1',
            'services' => 'required|array',
            'floors' => 'required|integer|min:1',
            'seat_row_number' => 'required|integer|min:1',
            'seat_column_number' => 'required|integer|min:1',
            'detail' => 'required|string',
            'priority' => 'required|integer',
            // Không validate slug
        ]);

        // --- Slug Generation ---
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        // Kiểm tra slug mới có trùng với bản ghi khác không (loại trừ chính nó)
        while (DB::table('buses')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        // --- JSON Handling ---
        // Images
        if (isset($validated['images']) && is_array($validated['images'])) {
            $validated['images'] = array_filter($validated['images'], fn($value) => $value !== null && $value !== '');
            $validated['images'] = !empty($validated['images']) ? json_encode(array_values($validated['images'])) : null;
        } else {
            $validated['images'] = null;
        }
        // Services
        if (isset($validated['services']) && is_array($validated['services'])) {
            $validated['services'] = array_filter($validated['services'], fn($value) => $value !== null && $value !== '');
            $validated['services'] = !empty($validated['services']) ? json_encode(array_values($validated['services'])) : null;
        } else {
            $validated['services'] = null;
        }


        DB::table('buses')->where('id', $id)->update($validated);

        return redirect()->route('admin.buses.index')->with('success', 'Loại xe đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bus = DB::table('buses')->find($id);
        if (!$bus) {
            return back()->with('error', 'Không tìm thấy Loại xe để xóa.');
        }

        DB::table('buses')->where('id', $id)->delete();

        return redirect()->route('admin.buses.index')->with('success', 'Loại xe đã được xóa thành công!');
    }
}
