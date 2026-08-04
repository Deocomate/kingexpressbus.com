<?php

namespace App\Support\Admin\OptionSources;

use Illuminate\Support\Facades\DB;

class Provinces implements OptionSourceContract
{
    public function search(string $query): array
    {
        return DB::table('provinces')
            ->select('id', 'name')
            ->where('name', 'like', '%'.$query.'%')
            ->limit(50)
            ->get()
            ->map(fn ($row) => ['id' => $row->id, 'text' => $row->name])
            ->all();
    }
}
