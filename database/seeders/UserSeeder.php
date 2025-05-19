<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();
        $countUser = DB::table('users')->get()->count();
        if ($countUser < 1) {
            User::insert([
                'name' => "Admin",
                'email' => "admin@gmail.com",
                'password' => bcrypt('admin')
            ]);
        }

        $faker = Faker::create();
        $data = [];

        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($data);
        echo "Seed User Success";
        return;
    }
}
