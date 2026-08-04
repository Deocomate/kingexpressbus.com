<?php

namespace App\Support\Admin\OptionSources;

use Illuminate\Support\Facades\DB;

class Buses implements OptionSourceContract
{
    public function search(string $query): array
    {
        return DB::table('buses')
            ->select('id', 'name')
            ->where('name', 'like', '%'.$query.'%')
            ->limit(50)
            ->get()
            ->map(fn ($row) => ['id' => $row->id, 'text' => $row->name])
            ->all();
    }
}
