<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KingExpressBusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Provinces ---
        $hanoiId = DB::table('provinces')->insertGetId([
            'name' => 'Hà Nội',
            'type' => 'thanhpho',
            'title' => 'Thủ đô Hà Nội',
            'description' => 'Thủ đô ngàn năm văn hiến.',
            'thumbnail' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg',
            'images' => json_encode(['/client/images/hanoi1.jpg', '/client/images/hanoi2.jpg']), // Placeholder
            'detail' => 'Chi tiết về Hà Nội...',
            'priority' => 1,
            'slug' => Str::slug('Hà Nội'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $laocaiId = DB::table('provinces')->insertGetId([
            'name' => 'Lào Cai',
            'type' => 'tinh',
            'title' => 'Tỉnh Lào Cai',
            'description' => 'Nơi có Sapa mờ sương.',
            'thumbnail' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg', // Placeholder
            'images' => json_encode(['/client/images/laocai1.jpg', '/client/images/laocai2.jpg']), // Placeholder
            'detail' => 'Chi tiết về Lào Cai...',
            'priority' => 2,
            'slug' => Str::slug('Lào Cai'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Provinces seeded!');

        // --- 2. Districts (Cần cho Stops) ---
        // Tạo một district đại diện cho Hà Nội (ví dụ: Bến xe Mỹ Đình)
        $hanoiDistrictId = DB::table('districts')->insertGetId([
            'province_id' => $hanoiId,
            'name' => 'Bến xe Mỹ Đình',
            'type' => 'benxe', // Hoặc 'quan' nếu là một quận cụ thể
            'title' => 'Bến xe Mỹ Đình - Hà Nội',
            'description' => 'Điểm xuất phát/đến chính tại Hà Nội.',
            'thumbnail' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg',
            'images' => json_encode([]),
            'detail' => 'Chi tiết Bến xe Mỹ Đình.',
            'priority' => 1,
            'slug' => Str::slug('Bến xe Mỹ Đình'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo district Sapa
        $sapaDistrictId = DB::table('districts')->insertGetId([
            'province_id' => $laocaiId,
            'name' => 'Thị xã Sa Pa', // Hoặc 'Sapa'
            'type' => 'thixa', // Hoặc 'diadiemdulich'
            'title' => 'Thị xã Sa Pa - Lào Cai',
            'description' => 'Thị xã du lịch nổi tiếng Sapa.',
            'thumbnail' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg',
            'images' => json_encode([]),
            'detail' => 'Chi tiết về Sapa.',
            'priority' => 1,
            'slug' => Str::slug('Thị xã Sa Pa'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->command->info('Districts seeded!');


        // --- 3. Routes ---
        $routeHanoiSapaId = DB::table('routes')->insertGetId([
            'province_id_start' => $hanoiId,
            'province_id_end' => $laocaiId,
            'title' => 'Hà Nội - Sapa (Lào Cai)',
            'description' => 'Tuyến xe khách từ Hà Nội đi Sapa, Lào Cai.',
            'thumbnail' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg', // Placeholder
            'images' => json_encode([]),
            'distance' => 320, // ~320 km
            'duration' => '5 - 6 tiếng',
            'start_price' => 270000, // Giá khởi điểm chung của tuyến (sẽ bị override bởi bus_routes.price)
            'detail' => 'Chi tiết tuyến đường Hà Nội - Sapa.',
            'priority' => 1,
            'slug' => Str::slug('Hà Nội Sapa Lào Cai'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $routeSapaHanoiId = DB::table('routes')->insertGetId([
            'province_id_start' => $laocaiId,
            'province_id_end' => $hanoiId,
            'title' => 'Sapa (Lào Cai) - Hà Nội',
            'description' => 'Tuyến xe khách từ Sapa, Lào Cai về Hà Nội.',
            'thumbnail' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg', // Placeholder
            'images' => json_encode([]),
            'distance' => 320,
            'duration' => '5 - 6 tiếng',
            'start_price' => 270000,
            'detail' => 'Chi tiết tuyến đường Sapa - Hà Nội.',
            'priority' => 2,
            'slug' => Str::slug('Sapa Lào Cai Hà Nội'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->command->info('Routes seeded!');

        // --- 4. Buses ---
        $busSleeperId = DB::table('buses')->insertGetId([
            'title' => 'Xe giường nằm phổ thông 40 chỗ',
            'description' => 'Dòng xe giường nằm tiêu chuẩn, 40 chỗ, đầy đủ tiện nghi cơ bản.',
            'thumbnail' => 'https://kingexpressbus.com/userfiles/files/king/9.jpg',
            'images' => json_encode(['https://kingexpressbus.com/userfiles/files/king/9.jpg']),
            'name' => 'Giường nằm 40 chỗ',
            'model_name' => 'Universe Thaco',
            'type' => 'sleeper',
            'number_of_seats' => 40,
            'services' => json_encode(['Điều hòa', 'Nước uống', 'Khăn lạnh']),
            'floors' => 2,
            'seat_row_number' => 10, // 10 hàng mỗi bên * 2 bên
            'seat_column_number' => 2, // 2 cột mỗi tầng
            'detail' => 'Chi tiết về xe giường nằm 40 chỗ...',
            'priority' => 1,
            'slug' => Str::slug('Giường nằm 40 chỗ'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $busVipSingleId = DB::table('buses')->insertGetId([
            'title' => 'Xe VIP Cabin đơn 20 chỗ',
            'description' => 'Xe giường nằm cabin VIP, mỗi hành khách một cabin riêng tư, hiện đại.',
            'thumbnail' => 'https://kingexpressbus.com/client/images/banner.png',
            'images' => json_encode(['https://kingexpressbus.com/client/images/banner.png']),
            'name' => 'VIP Cabin Đơn 20 chỗ',
            'model_name' => 'Limousine Cabin',
            'type' => 'cabin', // cabin đơn
            'number_of_seats' => 20,
            'services' => json_encode(['Wifi', 'TV LCD', 'Cổng sạc USB', 'Nước uống', 'Chăn đắp']),
            'floors' => 1, // Hoặc 2 nếu là cabin 2 tầng
            'seat_row_number' => 10, // 10 cabin mỗi bên
            'seat_column_number' => 1, // 1 cabin mỗi cột mỗi bên
            'detail' => 'Chi tiết về xe VIP Cabin đơn...',
            'priority' => 2,
            'slug' => Str::slug('VIP Cabin Đơn 20 chỗ'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $busVipDoubleId = DB::table('buses')->insertGetId([
            'title' => 'Xe VIP Cabin đôi 20 chỗ',
            'description' => 'Xe giường nằm cabin đôi VIP, không gian rộng rãi cho cặp đôi hoặc gia đình.',
            'thumbnail' => 'https://kingexpressbus.com/client/images/banner.png',
            'images' => json_encode(['https://kingexpressbus.com/client/images/banner.png']),
            'name' => 'VIP Cabin Đôi 20 chỗ',
            'model_name' => 'Limousine Cabin Đôi',
            'type' => 'doublecabin',
            'number_of_seats' => 20, // Tính theo số người
            'services' => json_encode(['Wifi', 'TV LCD lớn', 'Cổng sạc USB', 'Nước uống', 'Rèm che riêng tư']),
            'floors' => 1, // Hoặc 2
            'seat_row_number' => 5, // 5 cabin đôi mỗi bên
            'seat_column_number' => 1, // 1 cabin đôi mỗi cột mỗi bên
            'detail' => 'Chi tiết về xe VIP Cabin đôi...',
            'priority' => 3,
            'slug' => Str::slug('VIP Cabin Đôi 20 chỗ'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->command->info('Buses seeded!');

        // --- 5. Bus Routes (Schedules) ---
        $busRoutesData = [
            // Hà Nội - Sapa
            [
                'bus_id' => $busSleeperId, 'route_id' => $routeHanoiSapaId, 'title' => 'Hà Nội - Sapa (Sleeper 7:00)',
                'start_at' => '07:00:00', 'end_at' => '13:00:00', 'price' => 270000,
                'stops' => [['district_id' => $hanoiDistrictId, 'stop_at' => '07:00:00', 'title' => 'Xuất phát Bến xe Mỹ Đình'], ['district_id' => $sapaDistrictId, 'stop_at' => '13:00:00', 'title' => 'Trung tâm Sapa']]
            ],
            [
                'bus_id' => $busSleeperId, 'route_id' => $routeHanoiSapaId, 'title' => 'Hà Nội - Sapa (Sleeper 22:00)',
                'start_at' => '22:00:00', 'end_at' => '04:00:00', 'price' => 270000,
                'stops' => [['district_id' => $hanoiDistrictId, 'stop_at' => '22:00:00', 'title' => 'Xuất phát Bến xe Mỹ Đình'], ['district_id' => $sapaDistrictId, 'stop_at' => '04:00:00', 'title' => 'Trung tâm Sapa']]
            ],
            [
                'bus_id' => $busVipSingleId, 'route_id' => $routeHanoiSapaId, 'title' => 'Hà Nội - Sapa (VIP Đơn 7:00)',
                'start_at' => '07:00:00', 'end_at' => '12:30:00', 'price' => 450000,
                'stops' => [['district_id' => $hanoiDistrictId, 'stop_at' => '07:00:00', 'title' => 'Văn phòng Hà Nội'], ['district_id' => $sapaDistrictId, 'stop_at' => '12:30:00', 'title' => 'Văn phòng Sapa']]
            ],
            [
                'bus_id' => $busVipSingleId, 'route_id' => $routeHanoiSapaId, 'title' => 'Hà Nội - Sapa (VIP Đơn 22:00)',
                'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 450000,
                'stops' => [['district_id' => $hanoiDistrictId, 'stop_at' => '22:00:00', 'title' => 'Văn phòng Hà Nội'], ['district_id' => $sapaDistrictId, 'stop_at' => '03:30:00', 'title' => 'Văn phòng Sapa']]
            ],
            [
                'bus_id' => $busVipDoubleId, 'route_id' => $routeHanoiSapaId, 'title' => 'Hà Nội - Sapa (VIP Đôi 7:00)',
                'start_at' => '07:00:00', 'end_at' => '12:30:00', 'price' => 650000,
                'stops' => [['district_id' => $hanoiDistrictId, 'stop_at' => '07:00:00', 'title' => 'Văn phòng Hà Nội'], ['district_id' => $sapaDistrictId, 'stop_at' => '12:30:00', 'title' => 'Văn phòng Sapa']]
            ],
            [
                'bus_id' => $busVipDoubleId, 'route_id' => $routeHanoiSapaId, 'title' => 'Hà Nội - Sapa (VIP Đôi 22:00)',
                'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 650000,
                'stops' => [['district_id' => $hanoiDistrictId, 'stop_at' => '22:00:00', 'title' => 'Văn phòng Hà Nội'], ['district_id' => $sapaDistrictId, 'stop_at' => '03:30:00', 'title' => 'Văn phòng Sapa']]
            ],

            // Sapa - Hà Nội
            [
                'bus_id' => $busSleeperId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (Sleeper 14:00)',
                'start_at' => '14:00:00', 'end_at' => '20:00:00', 'price' => 270000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '14:00:00', 'title' => 'Xuất phát Trung tâm Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '20:00:00', 'title' => 'Bến xe Mỹ Đình']]
            ],
            [
                'bus_id' => $busSleeperId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (Sleeper 16:00)',
                'start_at' => '16:00:00', 'end_at' => '22:00:00', 'price' => 270000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '16:00:00', 'title' => 'Xuất phát Trung tâm Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '22:00:00', 'title' => 'Bến xe Mỹ Đình']]
            ],
            [
                'bus_id' => $busSleeperId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (Sleeper 22:00)',
                'start_at' => '22:00:00', 'end_at' => '04:00:00', 'price' => 270000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '22:00:00', 'title' => 'Xuất phát Trung tâm Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '04:00:00', 'title' => 'Bến xe Mỹ Đình']]
            ],
            [
                'bus_id' => $busVipSingleId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (VIP Đơn 14:00)',
                'start_at' => '14:00:00', 'end_at' => '19:30:00', 'price' => 450000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '14:00:00', 'title' => 'Văn phòng Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '19:30:00', 'title' => 'Văn phòng Hà Nội']]
            ],
            [
                'bus_id' => $busVipSingleId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (VIP Đơn 16:00)',
                'start_at' => '16:00:00', 'end_at' => '21:30:00', 'price' => 450000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '16:00:00', 'title' => 'Văn phòng Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '21:30:00', 'title' => 'Văn phòng Hà Nội']]
            ],
            [
                'bus_id' => $busVipSingleId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (VIP Đơn 22:00)',
                'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 450000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '22:00:00', 'title' => 'Văn phòng Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '03:30:00', 'title' => 'Văn phòng Hà Nội']]
            ],
            [
                'bus_id' => $busVipDoubleId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (VIP Đôi 14:00)',
                'start_at' => '14:00:00', 'end_at' => '19:30:00', 'price' => 650000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '14:00:00', 'title' => 'Văn phòng Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '19:30:00', 'title' => 'Văn phòng Hà Nội']]
            ],
            [
                'bus_id' => $busVipDoubleId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (VIP Đôi 16:00)',
                'start_at' => '16:00:00', 'end_at' => '21:30:00', 'price' => 650000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '16:00:00', 'title' => 'Văn phòng Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '21:30:00', 'title' => 'Văn phòng Hà Nội']]
            ],
            [
                'bus_id' => $busVipDoubleId, 'route_id' => $routeSapaHanoiId, 'title' => 'Sapa - Hà Nội (VIP Đôi 22:00)',
                'start_at' => '22:00:00', 'end_at' => '03:30:00', 'price' => 650000,
                'stops' => [['district_id' => $sapaDistrictId, 'stop_at' => '22:00:00', 'title' => 'Văn phòng Sapa'], ['district_id' => $hanoiDistrictId, 'stop_at' => '03:30:00', 'title' => 'Văn phòng Hà Nội']]
            ],
        ];

        foreach ($busRoutesData as $data) {
            $stops = $data['stops'];
            unset($data['stops']); // Xóa stops khỏi data chính của bus_route

            $busRouteId = DB::table('bus_routes')->insertGetId([
                'bus_id' => $data['bus_id'],
                'route_id' => $data['route_id'],
                'title' => $data['title'],
                'description' => 'Lịch trình ' . $data['title'] . ' với các điểm dừng tiện lợi.',
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'price' => $data['price'],
                'detail' => 'Chi tiết lịch trình cho ' . $data['title'],
                'priority' => 1,
                'slug' => Str::slug($data['title'] . '-' . $data['bus_id'] . '-' . $data['route_id'] . '-' . Str::random(5)), // Tạo slug unique
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // --- 6. Stops (Điểm dừng cho mỗi Bus Route) ---
            if ($busRouteId && !empty($stops)) {
                $stopsToInsert = [];
                foreach ($stops as $stop) {
                    $stopsToInsert[] = [
                        'bus_route_id' => $busRouteId,
                        'district_id' => $stop['district_id'],
                        'title' => $stop['title'] ?? $this->getDistrictName($stop['district_id']), // Lấy tên district nếu title stop trống
                        'stop_at' => $stop['stop_at'],
                    ];
                }
                if (!empty($stopsToInsert)) {
                    DB::table('stops')->insert($stopsToInsert);
                }
            }
        }
        $this->command->info('Bus Routes and Stops seeded!');

        // --- Web Info (Optional Default) ---
        if (DB::table('web_info')->count() == 0) {
            DB::table('web_info')->insert([
                'logo' => 'https://kingexpressbus.com/userfiles/files/web%20information/logo.jpg', // Cập nhật logo của bạn
                'title' => 'King Express Bus - Đặt vé xe trực tuyến',
                'description' => 'Hệ thống đặt vé xe khách Hà Nội - Sapa và các tuyến khác uy tín, chất lượng.',
                'email' => 'kingexpressbus@gmail.com',
                'phone' => '0924300366',
                'hotline' => '0924300366',
                'phone_detail' => 'Tổng đài đặt vé và CSKH: 092.430.0366',
                'web_link' => 'https://kingexpressbus.com',
                'facebook' => 'https://www.facebook.com/kingexpressbus',
                'zalo' => 'https://zalo.me/0924300366',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'map' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.558988986743!2d105.80150567500838!3d21.01034618063395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab6c7359486f%3A0x4a45f9fd9f6196d6!2zMTMgUC4gSOG7kyDEkOG6tWMgRGksIE5hbSBU4burIExpw6ptLCBIw6AgTuG7mWksIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1716265910687!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                'policy' => '<h1>Chính sách đặt vé</h1>',
                'detail' => 'King Express Bus tự hào là nhà xe cung cấp dịch vụ vận chuyển hành khách chất lượng cao.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Default Web Info seeded!');
        }

        // --- Menus (Optional Default) ---
        if (DB::table('menus')->count() == 0) {
            $menuTrangChu = DB::table('menus')->insertGetId(['name' => 'Trang chủ', 'url' => 'homepage', 'priority' => 1, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()]); // parent_id = 0 for root
            $menuLichTrinh = DB::table('menus')->insertGetId(['name' => 'Lịch trình', 'url' => '#', 'priority' => 2, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('menus')->insert(['name' => 'Hà Nội - Sapa', 'url' => '/tuyen-duong/ha-noi-sapa-lao-cai?departure_date=' . date('Y-m-d'), 'priority' => 1, 'parent_id' => $menuLichTrinh, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('menus')->insert(['name' => 'Sapa - Hà Nội', 'url' => '/tuyen-duong/sapa-lao-cai-ha-noi?departure_date=' . date('Y-m-d'), 'priority' => 2, 'parent_id' => $menuLichTrinh, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('menus')->insert(['name' => 'Tin tức', 'url' => '#', 'priority' => 3, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('menus')->insert(['name' => 'Liên hệ', 'url' => '#', 'priority' => 4, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()]);
            $this->command->info('Default Menus seeded!');
        }
    }

    private function getDistrictName(int $districtId): string
    {
        $district = DB::table('districts')->find($districtId);
        return $district ? $district->name : 'Điểm dừng không xác định';
    }
}
