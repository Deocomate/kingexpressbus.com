<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->tablesToTruncate() as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            WebProfileSeeder::class,
            ProvinceSeeder::class,
            DistrictTypeSeeder::class,
            BusServiceSeeder::class,
            BusSeeder::class,
            HolidaySurchargeSeeder::class,
            DistrictSeeder::class,
            StopSeeder::class,
            RouteSeeder::class,
            RouteStopSeeder::class,
            TripSeeder::class,
            HolidaySurchargeRouteSeeder::class,
            MenuSeeder::class,
        ]);
    }

    private function tablesToTruncate(): array
    {
        return [
            'bookings',
            'menus',
            'trips',
            'route_stops',
            'holiday_surcharge_routes',
            'routes',
            'stops',
            'districts',
            'holiday_surcharges',
            'bus_bus_service',
            'buses',
            'bus_services',
            'district_types',
            'provinces',
            'web_profiles',
            'users',
        ];
    }
}
