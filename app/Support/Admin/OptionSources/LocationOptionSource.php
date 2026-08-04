<?php

namespace App\Support\Admin\OptionSources;

use Illuminate\Support\Facades\DB;

/**
 * Option source for district types (loại địa điểm).
 * Registered under slug 'district-types' in AppServiceProvider.
 * Also provides a static helper for pre-loading small static option lists.
 */
class LocationOptionSource implements OptionSourceContract
{
    public function search(string $query): array
    {
        return DB::table('district_types')
            ->select('id', 'name')
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('priority', 'desc')
            ->limit(50)
            ->get()
            ->map(fn ($row) => ['id' => $row->id, 'text' => $row->name])
            ->all();
    }

    /**
     * Return all district types as [id => name] for static <select> options.
     * Use when the full list fits a dropdown without AJAX pagination.
     */
    public static function districtTypeOptions(): array
    {
        return DB::table('district_types')
            ->select('id', 'name')
            ->orderBy('priority', 'desc')
            ->get()
            ->pluck('name', 'id')
            ->all();
    }
}
