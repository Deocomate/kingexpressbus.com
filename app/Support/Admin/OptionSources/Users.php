<?php

namespace App\Support\Admin\OptionSources;

use Illuminate\Support\Facades\DB;

/**
 * Option source for admin users.
 * Only returns id + name — never email or phone.
 */
class Users implements OptionSourceContract
{
    public function search(string $query): array
    {
        return DB::table('users')
            ->select('id', 'name')
            ->where('name', 'like', '%' . $query . '%')
            ->limit(50)
            ->get()
            ->map(fn ($row) => ['id' => $row->id, 'text' => $row->name])
            ->all();
    }
}
