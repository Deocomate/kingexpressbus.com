<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
    data-accordion="false">

    <!-- ADMIN BASE -->
    <li class="nav-header">Admin</li>
    <x-menus.menu-bar :route="route('admin.dashboard.index')" name="Trang chủ" icon="fas fa-tachometer-alt">
        <x-menus.menu-item :route="route('admin.dashboard.index')" name="Tổng quan"></x-menus.menu-item>
    </x-menus.menu-bar>
    {{-- === Thêm mục Menu mới cho Quản lý Địa điểm === --}}
    <x-menus.menu-bar icon="fas fa-map-marked-alt" name="Quản lý Địa điểm" route="#">
        <x-menus.menu-item name="Tỉnh/Thành phố" :route="route('admin.provinces.index')"/>
        <x-menus.menu-item name="Quận/Huyện" :route="route('admin.districts.index')"/>
        {{-- <x-menus.menu-item name="Điểm dừng" :route="route('admin.stops.index')" /> --}}
    </x-menus.menu-bar>
    {{-- Trong menu.blade.php --}}
    <x-menus.menu-bar icon="fas fa-route" name="Quản lý Vận hành" route="#">
        <x-menus.menu-item name="Tuyến đường" :route="route('admin.routes.index')"/>
        <x-menus.menu-item name="Loại xe" :route="route('admin.buses.index')"/>
        <x-menus.menu-item name="Lịch trình xe" :route="route('admin.bus_routes.index')"/>
    </x-menus.menu-bar>
    <x-menus.menu-bar icon="fas fa-ticket-alt" name="Quản lý Đặt vé" :route="route('admin.bookings.index')">
        <x-menus.menu-item name="Vé đã đặt" :route="route('admin.bookings.index')"/>
    </x-menus.menu-bar>

    <x-menus.menu-bar icon="fas fa-list" name="Quản lý Menu" route="{{ route('admin.menus.index') }}">
        <x-menus.menu-item name="Danh sách Menu" :route="route('admin.menus.index')"/>
    </x-menus.menu-bar>
</ul>
