<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

// Thêm Collection để làm việc với kết quả DB dễ dàng hơn

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Trong phương thức boot, chúng ta lấy dữ liệu chung cần thiết cho các view
     * và chia sẻ chúng bằng View::share().
     */
    public function boot(): void
    {
        // Chia sẻ thông tin website chung (web_info)
        try {
            // Lấy bản ghi đầu tiên từ bảng web_info
            $webInfo = DB::table('web_info')->first();
            // Chia sẻ biến $webInfoGlobal với tất cả các view
            View::share('webInfoGlobal', $webInfo);
        } catch (\Exception $e) {
            // Xử lý trường hợp có lỗi (ví dụ: chưa chạy migration, lỗi kết nối DB)
            // Ghi log lỗi nếu cần: Log::error("Error fetching web_info: " . $e->getMessage());
            View::share('webInfoGlobal', null); // Chia sẻ giá trị null nếu có lỗi
        }


        // --- Lấy và xử lý dữ liệu Menu cho Client Header ---
        try {
            // Lấy tất cả các menu, sắp xếp theo độ ưu tiên và tên
            $allMenus = DB::table('menus')
                ->orderBy('priority', 'asc')
                ->orderBy('name', 'asc')
                ->get(); // Lấy dữ liệu dưới dạng Collection

            // Xây dựng cấu trúc cây menu từ danh sách menu phẳng
            $clientMenuTree = $this->buildMenuTree($allMenus);

            // Chia sẻ cấu trúc cây menu ($clientMenuTree) với tất cả các view
            // dưới tên biến là 'clientMenuTreeGlobal'
            View::share('clientMenuTreeGlobal', $clientMenuTree);

        } catch (\Exception $e) {
            // Xử lý trường hợp có lỗi khi lấy dữ liệu menu
            // Ghi log lỗi nếu cần: Log::error("Error fetching menus: " . $e->getMessage());
            View::share('clientMenuTreeGlobal', collect()); // Chia sẻ một Collection rỗng nếu có lỗi
        }
        // --- Kết thúc xử lý Menu ---

    }

    /**
     * Helper function to build the menu tree recursively.
     * Hàm này xây dựng cấu trúc cây từ danh sách menu phẳng.
     * Nó nhận vào một Collection các menu và ID của menu cha.
     * Nó trả về một Collection chứa các menu con trực tiếp, mỗi menu con
     * lại có thuộc tính 'children' chứa Collection các menu con của nó.
     *
     * @param Collection $elements Danh sách tất cả các menu từ DB (dạng Collection).
     * @param int|null $parentId ID của menu cha cần tìm con (null cho menu gốc).
     * @return Collection Cấu trúc cây menu (Collection các object menu, mỗi object có thể có 'children').
     */
    private function buildMenuTree(Collection $elements, $parentId = null): Collection
    {
        // Tạo một Collection rỗng để chứa các menu con tìm được ở cấp này
        $branch = collect();

        // Lọc ra các menu có parent_id khớp với $parentId được truyền vào
        // Sử dụng phương thức `where` của Collection
        $children = $elements->where('parent_id', $parentId);

        // Lặp qua từng menu con vừa lọc được
        foreach ($children as $child) {
            // Đệ quy: Gọi lại hàm buildMenuTree để tìm các menu con của menu $child hiện tại
            // Kết quả (là một Collection các menu con) được gán vào thuộc tính 'children' của $child
            $child->children = $this->buildMenuTree($elements, $child->id);

            // Thêm menu $child (đã có thuộc tính 'children') vào Collection $branch
            $branch->push($child);
        }

        // Trả về Collection chứa các menu con ở cấp hiện tại
        return $branch;
    }
}
