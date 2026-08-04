<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\DistrictType;
use App\Models\Province;
use App\Models\Stop;
use App\Support\Admin\Tables\DistrictTableConfig;
use App\Support\Admin\Tables\DistrictTypeTableConfig;
use App\Support\Admin\Tables\ProvinceTableConfig;
use App\Support\Admin\Tables\StopTableConfig;
use App\Support\Admin\TableQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    private const SECTIONS = ['provinces', 'district-types', 'districts', 'stops'];

    public function index(Request $request): View
    {
        $section = $request->input('section', 'provinces');

        if (! in_array($section, self::SECTIONS, true)) {
            $section = 'provinces';
        }

        $viewData = match ($section) {
            'provinces'      => $this->provincesData($request),
            'district-types' => $this->districtTypesData($request),
            'districts'      => $this->districtsData($request),
            'stops'          => $this->stopsData($request),
        };

        if ($request->header('X-Partial') === 'table') {
            return view($this->tablePartialView($section), $viewData);
        }

        return view('admin.locations.index', array_merge($viewData, [
            'section' => $section,
        ]));
    }

    private function tablePartialView(string $section): string
    {
        // Rows-only fragments (no [data-table] wrapper) — swapped via innerHTML
        // by the admin table engine, so the wrapper element itself must not
        // be re-rendered here.
        return match ($section) {
            'provinces'      => 'admin.locations._table-provinces-rows',
            'district-types' => 'admin.locations._table-district-types-rows',
            'districts'      => 'admin.locations._table-districts-rows',
            'stops'          => 'admin.locations._table-stops-rows',
        };
    }

    private function provincesData(Request $request): array
    {
        $config = ProvinceTableConfig::make();
        $tq = TableQuery::make(
            Province::query()->withCount('districts')->orderBy('priority', 'desc'),
            $config
        )->process($request);

        return ['paginator' => $tq->paginator(), 'tableQuery' => $tq];
    }

    private function districtTypesData(Request $request): array
    {
        $config = DistrictTypeTableConfig::make();
        $tq = TableQuery::make(DistrictType::query()->orderBy('priority', 'desc'), $config)->process($request);

        return ['paginator' => $tq->paginator(), 'tableQuery' => $tq];
    }

    private function districtsData(Request $request): array
    {
        $config = DistrictTableConfig::make();
        $tq = TableQuery::make(
            District::query()->with(['province', 'districtType'])->orderBy('priority', 'desc'),
            $config
        )->process($request);

        return ['paginator' => $tq->paginator(), 'tableQuery' => $tq];
    }

    private function stopsData(Request $request): array
    {
        $config = StopTableConfig::make();
        $tq = TableQuery::make(
            Stop::query()->with(['district'])->orderBy('priority', 'desc'),
            $config
        )->process($request);

        return ['paginator' => $tq->paginator(), 'tableQuery' => $tq];
    }
}
