<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySurchargeRouteSeeder extends Seeder
{
    public function run(): void
    {
        $rows = $this->rows();

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('holiday_surcharge_routes')->insert($chunk);
        }
    }

    private function rows(): array
    {
        return [];
    }
}
