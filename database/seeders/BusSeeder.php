<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    private const JSON_COLUMNS = ['image_list_url'];

    public function run(): void
    {
        $rows = $this->rows();

        $rows = $this->encodeJsonColumns($rows);

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('buses')->insert($chunk);
        }

        $this->insertServiceMappings();
    }

    private function insertServiceMappings(): void
    {
        $serviceIdsByBus = [
            1 => [2, 3, 4],
            4 => [2, 4],
            5 => [2, 4],
            6 => [2, 3, 4, 6],
            7 => [2, 3, 4, 6, 8, 9],
            8 => [2, 3, 4, 6, 8, 9],
        ];

        $rows = [];

        foreach ($serviceIdsByBus as $busId => $serviceIds) {
            foreach ($serviceIds as $serviceId) {
                $rows[] = [
                    'bus_id' => $busId,
                    'bus_service_id' => $serviceId,
                ];
            }
        }

        DB::table('bus_bus_service')->insert($rows);
    }

    private function encodeJsonColumns(array $rows): array
    {
        foreach ($rows as &$row) {
            foreach (self::JSON_COLUMNS as $column) {
                if (array_key_exists($column, $row) && is_array($row[$column])) {
                    $row[$column] = json_encode($row[$column], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }
        unset($row);

        return $rows;
    }

    private function rows(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Sleeper',
                'model_name' => 'Sleeper Bus',
                'seat_count' => 48,
                'thumbnail_url' => '/client/images/kingexpressbus/sleeper/1.jpg',
                'image_list_url' => ['/client/images/kingexpressbus/sleeper/1.jpg', '/client/images/kingexpressbus/sleeper/2.jpg', '/client/images/kingexpressbus/sleeper/3.jpg'],
                'content' => '<p>Xe giường nằm tiêu chuẩn là lựa chọn kinh tế cho các chuyến đi dài.</p>',
                'priority' => 1,
                'created_at' => '2025-10-02 23:27:41',
                'updated_at' => '2026-05-26 22:12:32',
            ],
            [
                'id' => 4,
                'name' => 'Seater',
                'model_name' => 'Seater Bus',
                'seat_count' => 48,
                'thumbnail_url' => '/client/images/kingexpressbus/seater/1.png',
                'image_list_url' => ['/client/images/kingexpressbus/seater/1.png', '/client/images/kingexpressbus/seater/2.png'],
                'content' => '<p>Xe ghế ngồi 16 chỗ phù hợp cho những chuyến đi ngắn, linh hoạt.</p>',
                'priority' => 6,
                'created_at' => '2025-10-02 23:27:41',
                'updated_at' => '2026-05-26 22:10:20',
            ],
            [
                'id' => 5,
                'name' => 'Limousine',
                'model_name' => 'Limousine Bus',
                'seat_count' => 9,
                'thumbnail_url' => '/client/images/kingexpressbus/limousine/1.png',
                'image_list_url' => ['/client/images/kingexpressbus/limousine/1.png', '/client/images/kingexpressbus/limousine/2.png'],
                'content' => '<p>Xe Limousine hiện đại với ghế hạng thương gia và dịch vụ cao cấp.</p>',
                'priority' => 5,
                'created_at' => '2025-10-02 23:27:41',
                'updated_at' => '2026-03-19 06:44:54',
            ],
            [
                'id' => 6,
                'name' => 'VIP 32 sleeper',
                'model_name' => 'VIP Sleeper 32',
                'seat_count' => 48,
                'thumbnail_url' => '/client/images/kingexpressbus/sleeper_vip_32/sleeper32-1.jpg',
                'image_list_url' => ['/client/images/kingexpressbus/sleeper_vip_32/sleeper32-1.jpg', '/client/images/kingexpressbus/sleeper_vip_32/sleeper32-2.jpg'],
                'content' => '<p>Xe giường nằm VIP 32 chỗ với rèm che riêng tư, không gian sang trọng.</p>',
                'priority' => 2,
                'created_at' => '2025-10-02 23:27:41',
                'updated_at' => '2026-05-26 22:11:18',
            ],
            [
                'id' => 7,
                'name' => 'VIP 22 cabin single',
                'model_name' => 'VIP Cabin Single 22',
                'seat_count' => 20,
                'thumbnail_url' => '/client/images/kingexpressbus/cabin/1.jpg',
                'image_list_url' => ['/client/images/kingexpressbus/cabin/1.jpg', '/client/images/kingexpressbus/cabin/3.jpg', '/client/images/kingexpressbus/cabin/4.jpg', '/client/images/kingexpressbus/cabin/5.jpg'],
                'content' => '<p>Cabin VIP đơn 22 giường đem lại sự riêng tư tuyệt đối với đầy đủ tiện nghi.</p>',
                'priority' => 3,
                'created_at' => '2025-10-02 23:27:41',
                'updated_at' => '2026-05-26 22:10:59',
            ],
            [
                'id' => 8,
                'name' => 'VIP 22 cabin double',
                'model_name' => 'VIP Cabin Double 22',
                'seat_count' => 20,
                'thumbnail_url' => '/client/images/kingexpressbus/cabin_double/1.jpg',
                'image_list_url' => ['/client/images/kingexpressbus/cabin_double/1.jpg', '/client/images/kingexpressbus/cabin_double/3.jpg', '/client/images/kingexpressbus/cabin_double/4.jpg', '/client/images/kingexpressbus/cabin_double/5.jpg'],
                'content' => '<p>Cabin VIP đôi 22 giường lý tưởng cho cặp đôi hoặc gia đình nhỏ.</p>',
                'priority' => 4,
                'created_at' => '2025-10-02 23:27:41',
                'updated_at' => '2026-05-26 22:10:46',
            ],
        ];
    }
}
