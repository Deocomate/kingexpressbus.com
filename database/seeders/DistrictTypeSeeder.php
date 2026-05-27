<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = $this->rows();

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('district_types')->insert($chunk);
        }
    }

    private function rows(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Bến xe',
                'priority' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Văn phòng',
                'priority' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Điểm đón trả',
                'priority' => 3,
            ],
            [
                'id' => 4,
                'name' => 'Quận',
                'priority' => 4,
            ],
            [
                'id' => 5,
                'name' => 'Xã',
                'priority' => 5,
            ],
            [
                'id' => 6,
                'name' => 'Thành Phố',
                'priority' => 0,
            ],
        ];
    }
}
