{{-- Tab bar --}}
<x-admin::table.tabs :tabs="$tabs" :activeTab="$activeTab" :badges="$tabBadges" />

{{-- Filters --}}
<form method="GET" action="{{ route('admin.trips.index') }}" class="flex flex-wrap gap-3 items-end px-1 py-3" data-table-filter>
    <input type="hidden" name="section" value="trips">
    <input type="hidden" name="tab" value="{{ $activeTab }}">

    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Tuyến đường, xe…"
               class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500">
    </div>

    <div class="min-w-[160px]">
        <label class="block text-xs font-medium text-gray-600 mb-1">Tuyến đường</label>
        <select name="filter[route_id]"
                class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">— Tất cả —</option>
            @foreach($routes as $route)
            <option value="{{ $route->id }}" @selected((string)$filterRouteId === (string)$route->id)>{{ $route->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="min-w-[160px]">
        <label class="block text-xs font-medium text-gray-600 mb-1">Xe</label>
        <select name="filter[bus_id]"
                class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="">— Tất cả —</option>
            @foreach($buses as $bus)
            <option value="{{ $bus->id }}" @selected((string)$filterBusId === (string)$bus->id)>{{ $bus->name }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="px-4 py-2 text-sm font-medium bg-gray-700 hover:bg-gray-800 text-white rounded shadow-sm transition-colors">Lọc</button>
    <a href="{{ route('admin.trips.index', ['section' => 'trips', 'tab' => $activeTab]) }}"
       class="px-4 py-2 text-sm font-medium bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded shadow-sm transition-colors">Xóa lọc</a>
</form>

{{-- Bulk action bar --}}
<form id="bulk-form" method="POST" action="{{ route('admin.trips.bulk-destroy') }}">
    @csrf @method('DELETE')
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        formAction="{{ route('admin.trips.bulk-destroy') }}"
    />

    {{-- Grouped table --}}
    <x-admin::table.table id="trips-table">
        @include('admin.trips._table-rows', compact('grouped', 'paginator'))
    </x-admin::table.table>
</form>
