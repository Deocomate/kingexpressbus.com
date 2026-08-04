{{-- Tab bar --}}
<x-admin::table.tabs :tabs="$tabs" :activeTab="$activeTab" :badges="$tabBadges" />

{{-- Filters --}}
<form method="GET" action="{{ route('admin.trips.index') }}" class="flex flex-wrap gap-3 items-end px-1 py-3">
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
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-8 px-4 py-3">
                        <input type="checkbox" data-select-all class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Giờ xuất bến</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Xe</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Giá vé</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">Trạng thái</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Độ ưu tiên</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($grouped as $routeName => $trips)
                    {{-- Group header --}}
                    <x-admin::table.group-header
                        :key="Str::slug($routeName)"
                        :label="$routeName"
                        :count="$trips->count()"
                        :colspan="7"
                    />
                    @foreach($trips as $trip)
                    <tr data-group-rows="{{ Str::slug($routeName) }}" class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="ids[]" value="{{ $trip->id }}"
                                   data-row-check class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        </td>
                        <td class="px-4 py-3 font-mono text-gray-700">
                            {{ substr($trip->start_time, 0, 5) }} → {{ substr($trip->end_time, 0, 5) }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $trip->bus_name }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($trip->price) }}đ</td>
                        <td class="px-4 py-3">
                            <x-admin::table.toggle-cell
                                :url="route('admin.trips.toggle-active', $trip->id)"
                                :value="(bool)$trip->is_active"
                                label="Bật/tắt hoạt động"
                            />
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ $trip->priority }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.trips.edit', $trip->id) }}"
                                   class="text-xs text-brand-600 hover:text-brand-800 font-medium">Sửa</a>
                                <form method="POST" action="{{ route('admin.trips.destroy', $trip->id) }}"
                                      onsubmit="return confirm('Xóa chuyến này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">Không có chuyến xe nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <x-admin::table.pagination :paginator="$paginator" />
    </x-admin::table.table>
</form>
