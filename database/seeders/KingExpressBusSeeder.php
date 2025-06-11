<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KingExpressBusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Temporarily disable foreign key checks to allow truncating tables in any order.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate all relevant tables to ensure a clean slate before seeding.
        // The order is reversed from dependency to be safe, though not strictly necessary with checks off.
        DB::table('bookings')->truncate();
        DB::table('customers')->truncate();
        DB::table('sessions')->truncate();
        DB::table('stops')->truncate();
        DB::table('bus_routes')->truncate();
        DB::table('buses')->truncate();
        DB::table('routes')->truncate();
        DB::table('districts')->truncate();
        DB::table('provinces')->truncate();
        DB::table('menus')->truncate();
        DB::table('web_info')->truncate();
        DB::table('users')->truncate();
        DB::table('password_reset_tokens')->truncate();
        DB::table('failed_jobs')->truncate();
        DB::table('jobs')->truncate();


        // 1. Seed the 'users' table
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'root', 'email' => 'root@gmail.com', 'email_verified_at' => null, 'password' => '$2y$12$0bGz3cTyD1c6wBSZylcSf.XrZQCJRqQu5TdbdtYQ5gA2QPdV8uogy', 'remember_token' => 'Ay3Vln9B795UUPOoRGdY9zGCl72dzqDBMlGLYqWDdI8Td1vQoaTzy7yF3h68', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'name' => 'Mrs. Rosemary Frami', 'email' => 'rharvey@example.net', 'email_verified_at' => null, 'password' => '$2y$12$PQnRB/YPw9juxwQHyDzb/OKDztYRJpmQ44ktQgNer6PpQ5mpkiSEO', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:19', 'updated_at' => '2025-06-11 01:18:19'],
            ['id' => 3, 'name' => 'Juwan Mayer', 'email' => 'malinda08@example.org', 'email_verified_at' => null, 'password' => '$2y$12$lB4HLMcF9Tsf5ctW/qCq/uChSvWHPd2QLoDAtWHQNcOhgDsmw4oWS', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:19', 'updated_at' => '2025-06-11 01:18:19'],
            ['id' => 4, 'name' => 'Dr. Sheridan Renner', 'email' => 'nat.hackett@example.com', 'email_verified_at' => null, 'password' => '$2y$12$s327XDuV0jFFYzlZUWSld.x4dmJ2bHHRgVqLdq3M3XNGGEr5wbJ.m', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:20', 'updated_at' => '2025-06-11 01:18:20'],
            ['id' => 5, 'name' => 'Sandy Parisian DVM', 'email' => 'qrogahn@example.net', 'email_verified_at' => null, 'password' => '$2y$12$MneoI8V1oxrzvcvKXC/L.e1Zh1cP2awqijUi1YHSVQLeJZPhDSP0W', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:20', 'updated_at' => '2025-06-11 01:18:20'],
            ['id' => 6, 'name' => 'Dr. Darian Conn II', 'email' => 'mrempel@example.com', 'email_verified_at' => null, 'password' => '$2y$12$VVykua8lGmPC7lIEp3FF6ebhS9OcIX62esZZRcee8Eqic66Mr2v2.', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:20', 'updated_at' => '2025-06-11 01:18:20'],
            ['id' => 7, 'name' => 'Theresia Marks', 'email' => 'flockman@example.net', 'email_verified_at' => null, 'password' => '$2y$12$QF7VQVlQ09lg/Vt.lbfBL.spIxFGHCtR1yYDYKSFeJsgSdnH.pJTa', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:20', 'updated_at' => '2025-06-11 01:18:20'],
            ['id' => 8, 'name' => 'August Crooks', 'email' => 'salvador11@example.com', 'email_verified_at' => null, 'password' => '$2y$12$i9aURLcrSRO61btGbkofj.AYi8dXfIKgw5NkYF2vnZ48/QRQc1Q2K', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:20', 'updated_at' => '2025-06-11 01:18:20'],
            ['id' => 9, 'name' => 'Reinhold Berge IV', 'email' => 'elisha.kuhic@example.com', 'email_verified_at' => null, 'password' => '$2y$12$3HngtpIlnk83y7Qo7ybKHuSQNUElcm0gykyIuppWO9cHkWNWyYEgO', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 10, 'name' => 'Dr. Ulises Zboncak', 'email' => 'stephen.bahringer@example.org', 'email_verified_at' => null, 'password' => '$2y$12$MYN.6PAFUq3SJKTeVouEU.fdWJGSoeyMCsS6Fz7YBWGsVBXrPzYQK', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 11, 'name' => 'Kenya Nolan', 'email' => 'ziemann.margaret@example.org', 'email_verified_at' => null, 'password' => '$2y$12$hQfKfS.4MdvipBF5ZJAZcORCwOe.3cG2ivlFwTH1XQZRJarCJpwiS', 'remember_token' => null, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
        ]);
        $this->command->info('Users table seeded.');

        // 2. Seed the 'web_info' table
        DB::table('web_info')->insert([
            ['id' => 1, 'logo' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg', 'title' => 'King Express Bus - Đặt vé xe trực tuyến', 'description' => 'Hệ thống đặt vé xe khách Hà Nội - Sapa và các tuyến khác uy tín, chất lượng.', 'email' => 'kingexpressbus@gmail.com', 'phone' => '0924300366', 'hotline' => '0924300366', 'phone_detail' => 'Tổng đài đặt vé và CSKH: 092.430.0366', 'web_link' => 'https://kingexpressbus.com', 'facebook' => 'https://www.facebook.com/kingexpressbus', 'zalo' => 'https://zalo.me/0924300366', 'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội', 'map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.558988986743!2d105.80150567500838!3d21.01034618063395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab6c7359486f%3A0x4a45f9fd9f6196d6!2zMTMgUC4gSOG7kyDEkOG6tWMgRGksIE5hbSBU4burIExpw6ptLCBIw6AgTuG7mWksIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1716265910687!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'policy' => '<h1>Chính sách đặt vé</h1>', 'detail' => 'King Express Bus tự hào là nhà xe cung cấp dịch vụ vận chuyển hành khách chất lượng cao.', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
        ]);
        $this->command->info('Web Info table seeded.');

        // 3. Seed the 'provinces' table
        DB::table('provinces')->insert([
            ['id' => 1, 'name' => 'Hà Nội', 'type' => 'thanhpho', 'title' => 'Thủ đô Hà Nội', 'description' => 'Thủ đô ngàn năm văn hiến.', 'thumbnail' => '/userfiles/files/king/9.jpg', 'images' => '["/userfiles/files/king/1.jpg"]', 'detail' => '<p>Chi tiết về Hà Nội...</p>', 'priority' => 1, 'slug' => 'ha-noi', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 2, 'name' => 'Sa Pa - Lào Cai', 'type' => 'tinh', 'title' => 'Sa Pa - Lào Cai', 'description' => 'Nơi có Sapa mờ sương.', 'thumbnail' => '/userfiles/files/king/9.jpg', 'images' => '["/userfiles/files/king/9.jpg"]', 'detail' => '<p>Chi tiết về Lào Cai...</p>', 'priority' => 2, 'slug' => 'sa-pa-lao-cai', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
        ]);
        $this->command->info('Provinces table seeded.');

        // 4. Seed the 'districts' table
        DB::table('districts')->insert([
            ['id' => 2, 'province_id' => 2, 'name' => 'Thị xã Sa Pa', 'type' => 'thixa', 'title' => 'Thị xã Sa Pa - Lào Cai', 'description' => 'Thị xã du lịch nổi tiếng Sapa.', 'thumbnail' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg', 'images' => '["/userfiles/files/king/1.jpg"]', 'detail' => '<p>Chi tiết về Sapa.</p>', 'priority' => 1, 'slug' => 'thi-xa-sa-pa', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 3, 'province_id' => 1, 'name' => 'Hà Nội', 'type' => 'thanhpho', 'title' => 'Hà Nội', 'description' => 'Hà Nội', 'thumbnail' => '/userfiles/files/king/1.jpg', 'images' => '["/userfiles/files/king/1.jpg"]', 'detail' => '<p>Hà Nội</p>', 'priority' => 1, 'slug' => 'ha-noi', 'created_at' => null, 'updated_at' => null],
        ]);
        $this->command->info('Districts table seeded.');

        // 5. Seed the 'routes' table
        DB::table('routes')->insert([
            ['id' => 1, 'province_id_start' => 1, 'province_id_end' => 2, 'title' => 'Hà Nội - Sapa (Lào Cai)', 'description' => 'Tuyến xe khách từ Hà Nội đi Sapa, Lào Cai.', 'thumbnail' => '/userfiles/files/king/1.jpg', 'images' => '["/userfiles/files/king/1.jpg"]', 'distance' => 320, 'duration' => '5 - 6 tiếng', 'start_price' => 270000, 'detail' => '<p>Chi tiết tuyến đường Hà Nội - Sapa.</p>', 'priority' => 1, 'slug' => 'ha-noi-sapa-lao-cai', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:50:33'],
            ['id' => 2, 'province_id_start' => 2, 'province_id_end' => 1, 'title' => 'Sapa (Lào Cai) - Hà Nội', 'description' => 'Tuyến xe khách từ Sapa, Lào Cai về Hà Nội.', 'thumbnail' => '/userfiles/files/king/9.jpg', 'images' => '["/userfiles/files/king/1.jpg"]', 'distance' => 320, 'duration' => '5 - 6 tiếng', 'start_price' => 270000, 'detail' => '<p>Chi tiết tuyến đường Sapa - Hà Nội.</p>', 'priority' => 2, 'slug' => 'sapa-lao-cai-ha-noi', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:51:21'],
        ]);
        $this->command->info('Routes table seeded.');

        // 6. Seed the 'stops' table
        DB::table('stops')->insert([
            ['id' => 10, 'route_id' => 1, 'district_id' => 2, 'title' => 'Trung tâm Sa Pa - 458 Điện Biên Phủ', 'created_at' => '2025-06-11 01:50:33', 'updated_at' => '2025-06-11 01:50:33'],
            ['id' => 11, 'route_id' => 1, 'district_id' => 3, 'title' => '204 Trần Quang Khải - Hoàn Kiếm', 'created_at' => '2025-06-11 01:50:33', 'updated_at' => '2025-06-11 01:50:33'],
            ['id' => 12, 'route_id' => 1, 'district_id' => 3, 'title' => 'Công viên Hoà Bình', 'created_at' => '2025-06-11 01:50:33', 'updated_at' => '2025-06-11 01:50:33'],
            ['id' => 13, 'route_id' => 1, 'district_id' => 3, 'title' => 'Sân bay Nội Bài', 'created_at' => '2025-06-11 01:50:33', 'updated_at' => '2025-06-11 01:50:33'],
            ['id' => 14, 'route_id' => 2, 'district_id' => 2, 'title' => 'Trung tâm Sapa - 458 Điện Biên Phủ', 'created_at' => '2025-06-11 01:51:21', 'updated_at' => '2025-06-11 01:51:21'],
            ['id' => 15, 'route_id' => 2, 'district_id' => 3, 'title' => '204 Trần Quang Khải - Hoàn Kiếm', 'created_at' => '2025-06-11 01:51:21', 'updated_at' => '2025-06-11 01:51:21'],
            ['id' => 16, 'route_id' => 2, 'district_id' => 3, 'title' => 'Công viên Hoà Bình', 'created_at' => '2025-06-11 01:51:21', 'updated_at' => '2025-06-11 01:51:21'],
            ['id' => 17, 'route_id' => 2, 'district_id' => 3, 'title' => 'Sân bay Nội Bài', 'created_at' => '2025-06-11 01:51:21', 'updated_at' => '2025-06-11 01:51:21'],
        ]);
        $this->command->info('Stops table seeded.');

        // 7. Seed the 'buses' table
        DB::table('buses')->insert([
            ['id' => 1, 'title' => 'Xe giường nằm phổ thông 40 chỗ', 'description' => 'Dòng xe giường nằm tiêu chuẩn, 40 chỗ, đầy đủ tiện nghi cơ bản.', 'thumbnail' => 'https://kingexpressbus.com/userfiles/files/king/9.jpg', 'images' => '["https://kingexpressbus.com/userfiles/files/king/9.jpg"]', 'name' => 'Giường nằm 40 chỗ', 'model_name' => 'Universe Thaco', 'type' => 'sleeper', 'number_of_seats' => 42, 'services' => '["Điều hòa", "Nước uống", "Khăn lạnh"]', 'floors' => 2, 'seat_row_number' => 7, 'seat_column_number' => 3, 'detail' => 'Chi tiết về xe giường nằm 40 chỗ...', 'priority' => 1, 'slug' => 'giuong-nam-40-cho', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 2, 'title' => 'Xe VIP Cabin đơn 20 chỗ', 'description' => 'Xe giường nằm cabin VIP, mỗi hành khách một cabin riêng tư, hiện đại.', 'thumbnail' => 'https://kingexpressbus.com/client/images/banner.png', 'images' => '["https://kingexpressbus.com/client/images/banner.png"]', 'name' => 'VIP Cabin Đơn 20 chỗ', 'model_name' => 'Limousine Cabin', 'type' => 'cabin', 'number_of_seats' => 20, 'services' => '["Wifi", "TV LCD", "Cổng sạc USB", "Nước uống", "Chăn đắp"]', 'floors' => 2, 'seat_row_number' => 5, 'seat_column_number' => 2, 'detail' => 'Chi tiết về xe VIP Cabin đơn...', 'priority' => 2, 'slug' => 'vip-cabin-don-20-cho', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 3, 'title' => 'Xe VIP Cabin đôi 20 chỗ', 'description' => 'Xe giường nằm cabin đôi VIP, không gian rộng rãi cho cặp đôi hoặc gia đình.', 'thumbnail' => 'https://kingexpressbus.com/client/images/banner.png', 'images' => '["https://kingexpressbus.com/client/images/banner.png"]', 'name' => 'VIP Cabin Đôi 20 chỗ', 'model_name' => 'Limousine Cabin Đôi', 'type' => 'doublecabin', 'number_of_seats' => 20, 'services' => '["Wifi", "TV LCD lớn", "Cổng sạc USB", "Nước uống", "Rèm che riêng tư"]', 'floors' => 2, 'seat_row_number' => 5, 'seat_column_number' => 2, 'detail' => 'Chi tiết về xe VIP Cabin đôi...', 'priority' => 3, 'slug' => 'vip-cabin-doi-20-cho', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
        ]);
        $this->command->info('Buses table seeded.');

        // 8. Seed the 'bus_routes' table
        DB::table('bus_routes')->insert([
            ['id' => 1, 'bus_id' => 1, 'route_id' => 1, 'title' => 'Hà Nội - Sapa (Sleeper 7:00)', 'description' => 'Lịch trình Hà Nội - Sapa (Sleeper 7:00) với các điểm dừng tiện lợi.', 'start_at' => '07:00:00', 'end_at' => '13:00:00', 'price' => 270000, 'detail' => 'Chi tiết lịch trình cho Hà Nội - Sapa (Sleeper 7:00)', 'priority' => 1, 'slug' => 'ha-noi-sapa-sleeper-700-1-1-clqxy', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 2, 'bus_id' => 1, 'route_id' => 1, 'title' => 'Hà Nội - Sapa (Sleeper 22:00)', 'description' => 'Lịch trình Hà Nội - Sapa (Sleeper 22:00) với các điểm dừng tiện lợi.', 'start_at' => '22:00:00', 'end_at' => '04:00:00', 'price' => 270000, 'detail' => 'Chi tiết lịch trình cho Hà Nội - Sapa (Sleeper 22:00)', 'priority' => 1, 'slug' => 'ha-noi-sapa-sleeper-2200-1-1-q5up0', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 3, 'bus_id' => 2, 'route_id' => 1, 'title' => 'Hà Nội - Sapa (VIP Đơn 7:00)', 'description' => 'Lịch trình Hà Nội - Sapa (VIP Đơn 7:00) với các điểm dừng tiện lợi.', 'start_at' => '07:00:00', 'end_at' => '12:30:00', 'price' => 450000, 'detail' => 'Chi tiết lịch trình cho Hà Nội - Sapa (VIP Đơn 7:00)', 'priority' => 1, 'slug' => 'ha-noi-sapa-vip-don-700-2-1-kp1pf', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 4, 'bus_id' => 2, 'route_id' => 1, 'title' => 'Hà Nội - Sapa (VIP Đơn 22:00)', 'description' => 'Lịch trình Hà Nội - Sapa (VIP Đơn 22:00) với các điểm dừng tiện lợi.', 'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 450000, 'detail' => 'Chi tiết lịch trình cho Hà Nội - Sapa (VIP Đơn 22:00)', 'priority' => 1, 'slug' => 'ha-noi-sapa-vip-don-2200-2-1-cnj5z', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 5, 'bus_id' => 3, 'route_id' => 1, 'title' => 'Hà Nội - Sapa (VIP Đôi 7:00)', 'description' => 'Lịch trình Hà Nội - Sapa (VIP Đôi 7:00) với các điểm dừng tiện lợi.', 'start_at' => '07:00:00', 'end_at' => '12:30:00', 'price' => 650000, 'detail' => 'Chi tiết lịch trình cho Hà Nội - Sapa (VIP Đôi 7:00)', 'priority' => 1, 'slug' => 'ha-noi-sapa-vip-doi-700-3-1-zffbt', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 6, 'bus_id' => 3, 'route_id' => 1, 'title' => 'Hà Nội - Sapa (VIP Đôi 22:00)', 'description' => 'Lịch trình Hà Nội - Sapa (VIP Đôi 22:00) với các điểm dừng tiện lợi.', 'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 650000, 'detail' => 'Chi tiết lịch trình cho Hà Nội - Sapa (VIP Đôi 22:00)', 'priority' => 1, 'slug' => 'ha-noi-sapa-vip-doi-2200-3-1-imi6x', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 7, 'bus_id' => 1, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (Sleeper 14:00)', 'description' => 'Lịch trình Sapa - Hà Nội (Sleeper 14:00) với các điểm dừng tiện lợi.', 'start_at' => '14:00:00', 'end_at' => '20:00:00', 'price' => 270000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (Sleeper 14:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-sleeper-1400-1-2-wy1bb', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 8, 'bus_id' => 1, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (Sleeper 16:00)', 'description' => 'Lịch trình Sapa - Hà Nội (Sleeper 16:00) với các điểm dừng tiện lợi.', 'start_at' => '16:00:00', 'end_at' => '22:00:00', 'price' => 270000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (Sleeper 16:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-sleeper-1600-1-2-umd6u', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 9, 'bus_id' => 1, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (Sleeper 22:00)', 'description' => 'Lịch trình Sapa - Hà Nội (Sleeper 22:00) với các điểm dừng tiện lợi.', 'start_at' => '22:00:00', 'end_at' => '04:00:00', 'price' => 270000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (Sleeper 22:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-sleeper-2200-1-2-rbksq', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 10, 'bus_id' => 2, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (VIP Đơn 14:00)', 'description' => 'Lịch trình Sapa - Hà Nội (VIP Đơn 14:00) với các điểm dừng tiện lợi.', 'start_at' => '14:00:00', 'end_at' => '19:30:00', 'price' => 450000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (VIP Đơn 14:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-vip-don-1400-2-2-u4l5n', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 11, 'bus_id' => 2, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (VIP Đơn 16:00)', 'description' => 'Lịch trình Sapa - Hà Nội (VIP Đơn 16:00) với các điểm dừng tiện lợi.', 'start_at' => '16:00:00', 'end_at' => '21:30:00', 'price' => 450000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (VIP Đơn 16:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-vip-don-1600-2-2-jxuyf', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 12, 'bus_id' => 2, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (VIP Đơn 22:00)', 'description' => 'Lịch trình Sapa - Hà Nội (VIP Đơn 22:00) với các điểm dừng tiện lợi.', 'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 450000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (VIP Đơn 22:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-vip-don-2200-2-2-winux', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 13, 'bus_id' => 3, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (VIP Đôi 14:00)', 'description' => 'Lịch trình Sapa - Hà Nội (VIP Đôi 14:00) với các điểm dừng tiện lợi.', 'start_at' => '14:00:00', 'end_at' => '19:30:00', 'price' => 650000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (VIP Đôi 14:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-vip-doi-1400-3-2-vpwxo', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 14, 'bus_id' => 3, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (VIP Đôi 16:00)', 'description' => 'Lịch trình Sapa - Hà Nội (VIP Đôi 16:00) với các điểm dừng tiện lợi.', 'start_at' => '16:00:00', 'end_at' => '21:30:00', 'price' => 650000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (VIP Đôi 16:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-vip-doi-1600-3-2-hexem', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 15, 'bus_id' => 3, 'route_id' => 2, 'title' => 'Sapa - Hà Nội (VIP Đôi 22:00)', 'description' => 'Lịch trình Sapa - Hà Nội (VIP Đôi 22:00) với các điểm dừng tiện lợi.', 'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 650000, 'detail' => 'Chi tiết lịch trình cho Sapa - Hà Nội (VIP Đôi 22:00)', 'priority' => 1, 'slug' => 'sapa-ha-noi-vip-doi-2200-3-2-xq5wo', 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
        ]);
        $this->command->info('Bus Routes table seeded.');

        // 9. Seed the 'menus' table
        DB::table('menus')->insert([
            ['id' => 1, 'name' => 'Trang chủ', 'url' => 'homepage', 'priority' => 1, 'parent_id' => null, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 2, 'name' => 'Lịch trình', 'url' => '#', 'priority' => 2, 'parent_id' => null, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 3, 'name' => 'Hà Nội - Sapa', 'url' => '/tuyen-duong/ha-noi-sapa-lao-cai?departure_date=2025-06-11', 'priority' => 1, 'parent_id' => 2, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 4, 'name' => 'Sapa - Hà Nội', 'url' => '/tuyen-duong/sapa-lao-cai-ha-noi?departure_date=2025-06-11', 'priority' => 2, 'parent_id' => 2, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 5, 'name' => 'Tin tức', 'url' => '#', 'priority' => 3, 'parent_id' => null, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
            ['id' => 6, 'name' => 'Liên hệ', 'url' => '#', 'priority' => 4, 'parent_id' => null, 'created_at' => '2025-06-11 01:18:21', 'updated_at' => '2025-06-11 01:18:21'],
        ]);
        $this->command->info('Menus table seeded.');

        // Re-enable foreign key checks after seeding is complete.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Database seeding completed successfully.');
    }
}
