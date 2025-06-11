<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = DB::table('routes')
            ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
            ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
            ->select(
                'routes.*',
                'p_start.name as province_start_name',
                'p_end.name as province_end_name'
            )
            ->orderBy('routes.priority', 'asc')
            ->orderBy('routes.id', 'desc')
            ->get();

        return view('kingexpressbus.admin.modules.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinces = DB::table('provinces')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $districtsByProvince = DB::table('districts')
            ->join('provinces', 'districts.province_id', '=', 'provinces.id')
            ->select('districts.id', 'districts.name', 'districts.type', 'provinces.name as province_name')
            ->orderBy('provinces.name', 'asc')
            ->orderBy('districts.priority', 'asc')
            ->orderBy('districts.name', 'asc')
            ->get()
            ->groupBy('province_name');

        $route = null;
        $existingStops = collect();

        return view('kingexpressbus.admin.modules.routes.createOrEdit', compact('provinces', 'route', 'districtsByProvince', 'existingStops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
                Rule::unique('routes')
            ],
            'stops' => 'nullable|array',
            'stops.*.district_id' => 'required_with:stops|exists:districts,id',
            'stops.*.title' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $routeData = Arr::except($validated, ['stops']);

            if (empty($routeData['slug'])) {
                $baseSlug = Str::slug($routeData['title']);
                $slug = $baseSlug;
                $counter = 1;
                while (DB::table('routes')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $routeData['slug'] = $slug;
            }

            if (isset($routeData['images']) && is_array($routeData['images'])) {
                $routeData['images'] = array_filter($routeData['images'], fn($value) => $value !== null && $value !== '');
                $routeData['images'] = !empty($routeData['images']) ? json_encode(array_values($routeData['images'])) : null;
            } else {
                $routeData['images'] = null;
            }

            $routeData['created_at'] = now();
            $routeData['updated_at'] = now();

            $routeId = DB::table('routes')->insertGetId($routeData);

            if ($request->has('stops') && is_array($request->input('stops'))) {
                $stopsData = [];
                foreach ($request->input('stops') as $stop) {
                    if (!empty($stop['district_id'])) {
                        $stopsData[] = [
                            'route_id' => $routeId,
                            'district_id' => $stop['district_id'],
                            'title' => $stop['title'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($stopsData)) {
                    DB::table('stops')->insert($stopsData);
                }
            }
        });

        return redirect()->route('admin.routes.index')->with('success', 'Tuyến đường và các điểm dừng đã được tạo thành công!');
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

        $districtsByProvince = DB::table('districts')
            ->join('provinces', 'districts.province_id', '=', 'provinces.id')
            ->select('districts.id', 'districts.name', 'districts.type', 'provinces.name as province_name')
            ->orderBy('provinces.name', 'asc')
            ->orderBy('districts.priority', 'asc')
            ->orderBy('districts.name', 'asc')
            ->get()
            ->groupBy('province_name');

        $existingStops = DB::table('stops')
            ->where('route_id', $id)
            ->get();

        return view('kingexpressbus.admin.modules.routes.createOrEdit', compact('provinces', 'route', 'districtsByProvince', 'existingStops'));
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
                Rule::unique('routes')->ignore($id)
            ],
            'stops' => 'nullable|array',
            'stops.*.district_id' => 'required_with:stops|exists:districts,id',
            'stops.*.title' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $validated, $id) {
            $routeData = Arr::except($validated, ['stops']);

            if (empty($routeData['slug'])) {
                $baseSlug = Str::slug($routeData['title']);
                $slug = $baseSlug;
                $counter = 1;
                while (DB::table('routes')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $routeData['slug'] = $slug;
            }

            if (isset($routeData['images']) && is_array($routeData['images'])) {
                $routeData['images'] = array_filter($routeData['images'], fn($value) => $value !== null && $value !== '');
                $routeData['images'] = !empty($routeData['images']) ? json_encode(array_values($routeData['images'])) : null;
            } else {
                $routeData['images'] = null;
            }

            $routeData['updated_at'] = now();

            DB::table('routes')->where('id', $id)->update($routeData);

            // Update Stops: Delete old ones and insert new ones
            DB::table('stops')->where('route_id', $id)->delete();
            if ($request->has('stops') && is_array($request->input('stops'))) {
                $stopsData = [];
                foreach ($request->input('stops') as $stop) {
                    if (!empty($stop['district_id'])) {
                        $stopsData[] = [
                            'route_id' => $id,
                            'district_id' => $stop['district_id'],
                            'title' => $stop['title'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($stopsData)) {
                    DB::table('stops')->insert($stopsData);
                }
            }
        });

        return redirect()->route('admin.routes.index')->with('success', 'Tuyến đường và các điểm dừng đã được cập nhật thành công!');
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
