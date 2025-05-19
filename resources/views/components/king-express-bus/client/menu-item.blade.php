{{--
    File Partial View cho Menu Item (Đệ quy)
    ========================================
    - File này chịu trách nhiệm hiển thị một mục menu (`$item`).
    - Nó được gọi từ view header chính và tự gọi lại chính nó để hiển thị các menu con.
    - Sử dụng Alpine.js cho hiệu ứng dropdown trên mobile.
    - Nhận vào:
        - $item: Object menu (chứa id, name, url, parent_id, children)
--}}
@props(['item'])

@php
    // Kiểm tra xem mục menu hiện tại có danh sách con không rỗng hay không
    $hasChildren = !empty($item->children) && $item->children->isNotEmpty();

    // Xác định URL cho thẻ <a>:
    // 1. Nếu $item->url tồn tại:
    //    - Kiểm tra xem $item->url có phải là tên của một route đã đăng ký không (Route::has()).
    //    - Nếu là tên route, tạo URL bằng route($item->url).
    //    - Nếu không phải tên route, coi nó là một đường dẫn bình thường và tạo URL bằng url($item->url).
    // 2. Nếu $item->url không tồn tại hoặc rỗng, đặt URL là '#' (link vô hiệu).
    $url = $item->url ? (Route::has($item->url) ? route($item->url) : url($item->url)) : '#';

    // Xác định xem link có nên được coi là 'active' hay không (ví dụ: dựa trên URL hiện tại)
    // Cách đơn giản: kiểm tra URL hiện tại có khớp với URL của menu item không
    // Lưu ý: Cách này có thể cần điều chỉnh tùy thuộc vào cấu trúc URL của bạn
    // $isActive = request()->fullUrlIs($url) || (request()->is(trim($url, '/')) && trim($url, '/') !== '');
    // Hoặc dùng cách kiểm tra route name nếu $item->url là route name
    $isActive = $item->url && Route::has($item->url) && request()->routeIs($item->url);

@endphp

{{-- Render Menu Item --}}
<div class="menu-item-container"> {{-- Thêm container để dễ quản lý --}}

    {{-- Trường hợp Menu Item CÓ CON (Sử dụng Alpine.js cho dropdown mobile) --}}
    @if($hasChildren)
        <div x-data="{ open: false }" class="relative">
            {{-- Nút bấm để mở/đóng submenu (chỉ hoạt động trên mobile) --}}
            {{-- Thêm class `menu-parent-item` để có thể style riêng nếu cần --}}
            <button @click="open = !open"
                    :class="{ 'bg-yellow-700 text-yellow-100': open, 'text-white hover:bg-yellow-700 hover:text-yellow-100': !open }"
                    class="w-full text-left px-4 py-2 font-medium flex justify-between items-center transition-colors duration-150 menu-parent-item {{ $isActive ? 'bg-yellow-700 text-yellow-100' : '' }}">
                <span>{{ $item->name }}</span>
                {{-- Icon mũi tên (xoay khi mở) --}}
                <svg class="w-4 h-4 transform transition-transform duration-200" :class="{ 'rotate-180': open }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            {{-- Panel chứa Submenu (chỉ hiển thị khi 'open' là true) --}}
            {{-- Thêm class `submenu-panel` --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                 class="pl-4 py-1 bg-yellow-700 rounded-b-md submenu-panel" {{-- Nền đậm hơn cho submenu --}}
                 style="display: none;" {{-- Ẩn ban đầu để tránh FOUC (Flash of Unstyled Content) --}}
            >
                {{-- Render các menu con bằng cách gọi lại chính partial view này --}}
                @foreach($item->children as $child)
                    {{-- Truyền $child vào component/partial --}}
                    <x-king-express-bus.client.menu-item :item="$child"/>
                @endforeach
            </div>
        </div>
    @else
        {{-- Trường hợp Menu Item KHÔNG CÓ CON (Link đơn giản) --}}
        {{-- Thêm class `menu-single-item` --}}
        <a href="{{ $url }}"
           class="block px-4 py-2 font-medium transition-colors duration-150 menu-single-item {{ $isActive ? 'bg-yellow-700 text-yellow-100' : 'text-white hover:bg-yellow-700 hover:text-yellow-100' }}">
            {{ $item->name }}
        </a>
    @endif

</div>
