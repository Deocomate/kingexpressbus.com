<?php

namespace App\View\Components\KingExpressBus\Client;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

// Sử dụng Collection
use Illuminate\Support\Facades\DB;

// Sử dụng DB Facade
use Illuminate\View\Component;

class SearchBar extends Component
{
    /**
     * Danh sách các địa điểm (Tỉnh/Thành phố và Quận/Huyện) cho dropdown.
     * Thuộc tính public sẽ tự động được truyền vào view.
     *
     * @var Collection
     */
    public Collection $locations;

    /**
     * Create a new component instance.
     * Lấy dữ liệu địa điểm từ DB trong constructor.
     */
    public function __construct()
    {
        $this->locations = $this->getLocations();
    }

    /**
     * Lấy và định dạng danh sách địa điểm từ DB.
     * Bao gồm Tỉnh/Thành phố và các Quận/Huyện/Địa điểm liên quan.
     *
     * @return Collection
     */
    private function getLocations(): Collection
    {
        try {
            // Lấy tất cả tỉnh/thành phố, sắp xếp theo ưu tiên, tên
            $provinces = DB::table('provinces')
                ->orderBy('priority', 'asc')
                ->orderBy('name', 'asc')
                ->select('id', 'name', 'type') // Lấy các cột cần thiết
                ->get();

            // Lấy các quận/huyện/địa điểm quan trọng (ví dụ: không lấy xã/phường nếu có)
            // Lọc các loại phù hợp cho điểm đi/đến
            $districts = DB::table('districts')
                ->whereIn('type', ['quan', 'huyen', 'thanhpho', 'thixa', 'benxe', 'sanbay', 'diadiemdulich']) // Lọc các loại phù hợp
                ->orderBy('province_id', 'asc')
                ->orderBy('priority', 'asc')
                ->orderBy('name', 'asc')
                ->select('id', 'name', 'type', 'province_id') // Lấy các cột cần thiết
                ->get();

            // Nhóm các quận/huyện theo tỉnh
            $districtsByProvince = $districts->groupBy('province_id');

            // Tạo collection cuối cùng cho dropdown
            $formattedLocations = collect();

            foreach ($provinces as $province) {
                // Thêm tỉnh/thành phố vào danh sách
                $formattedLocations->push([
                    'id' => 'province_' . $province->id, // Tạo ID unique cho tỉnh
                    'name' => $province->name . ($province->type === 'thanhpho' ? ' (Thành phố)' : ' (Tỉnh)'),
                    'group' => 'Tỉnh/Thành phố', // Nhóm để hiển thị trong optgroup
                    'province_id' => $province->id // Lưu lại province_id để xử lý ở backend nếu cần
                ]);

                // Thêm các quận/huyện thuộc tỉnh đó
                if (isset($districtsByProvince[$province->id])) {
                    foreach ($districtsByProvince[$province->id] as $district) {
                        // Định dạng tên loại cho dễ hiểu
                        $typeLabel = match ($district->type) {
                            'quan' => 'Quận',
                            'huyen' => 'Huyện',
                            'thanhpho' => 'TP.', // Thành phố thuộc tỉnh
                            'thixa' => 'Thị xã',
                            'benxe' => 'Bến xe',
                            'sanbay' => 'Sân bay',
                            'diadiemdulich' => 'Địa điểm DL',
                            default => ucfirst($district->type),
                        };
                        $formattedLocations->push([
                            'id' => 'district_' . $district->id, // Tạo ID unique cho quận/huyện
                            'name' => $district->name . " ({$typeLabel}, {$province->name})", // Hiển thị rõ ràng hơn
                            'group' => $province->name, // Nhóm theo tên tỉnh
                            'province_id' => $province->id // Lưu lại province_id
                        ]);
                    }
                }
            }

            // Sắp xếp lại collection theo group (Tỉnh/TP lên đầu) rồi đến tên
            return $formattedLocations->sortBy(['group', 'name'])->values(); // values() để reset keys

        } catch (\Exception $e) {
            // Xử lý lỗi nếu không lấy được dữ liệu
            // Log::error("Error fetching locations for SearchBar: " . $e->getMessage());
            return collect(); // Trả về collection rỗng
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // Truyền dữ liệu $locations (đã được gán trong constructor) vào view
        return view('components.king-express-bus.client.search-bar');
    }
}
