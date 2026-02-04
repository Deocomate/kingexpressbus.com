<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-header">QUẢN LÝ TỔNG QUAN</li>

    <x-menus.menu-bar
        :route="route('admin.dashboard.index')"
        name="Tổng quan"
        icon="fas fa-tachometer-alt"
        :route-group="['admin.dashboard.*']"/>

    <x-menus.menu-bar
        :route="route('admin.bookings.index')"
        name="Quản lý Đặt vé"
        icon="fas fa-ticket-alt"
        :route-group="['admin.bookings.*']"/>

    <li class="nav-header">QUẢN LÝ VẬN HÀNH</li>

    <x-menus.menu-bar
        :route="route('admin.buses.index')"
        name="Quản lý Đội xe"
        icon="fas fa-bus"
        :route-group="['admin.buses.*']"/>

    <x-menus.menu-bar
        :route="route('admin.routes.index')"
        name="Quản lý Tuyến đường"
        icon="fas fa-route"
        :route-group="['admin.routes.*']"/>

    <x-menus.menu-bar
        :route="route('admin.trips.index')"
        name="Quản lý Chuyến xe"
        icon="fas fa-calendar-alt"
        :route-group="['admin.trips.*']"/>

    <li class="nav-header">QUẢN LÝ DANH MỤC</li>

    <x-menus.menu-bar
        icon="fas fa-map-marked-alt"
        name="Quản lý Địa điểm"
        :route-group="['admin.provinces.*', 'admin.districts.*', 'admin.district-types.*', 'admin.stops.*']">
        <x-menus.menu-item name="Tỉnh/Thành phố" :route="route('admin.provinces.index')"/>
        <x-menus.menu-item name="Quận/Huyện" :route="route('admin.districts.index')"/>
        <x-menus.menu-item name="Loại địa điểm" :route="route('admin.district-types.index')"/>
        <x-menus.menu-item name="Điểm dừng" :route="route('admin.stops.index')"/>
    </x-menus.menu-bar>

    <x-menus.menu-bar
        :route="route('admin.bus-services.index')"
        name="Dịch vụ Xe"
        icon="fas fa-concierge-bell"
        :route-group="['admin.bus-services.*']"/>

    <li class="nav-header">HỆ THỐNG</li>

    <x-menus.menu-bar
        icon="fas fa-cogs"
        name="Giao diện & Cài đặt"
        :route-group="['admin.menus.*', 'admin.web_profiles.*']">
        <x-menus.menu-item name="Quản lý Menu" :route="route('admin.menus.index')"/>
        <x-menus.menu-item name="Thông tin Website" :route="route('admin.web_profiles.index')"/>
    </x-menus.menu-bar>
</ul>
