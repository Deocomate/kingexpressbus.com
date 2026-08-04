@extends('admin.layouts.app')

@section('title', 'Tuyến đường')
@section('breadcrumb')
    <span class="text-gray-700 font-medium">Tuyến đường</span>
@endsection

@section('content')
<div
    class="space-y-4"
    x-data="{
        selected: [],
        allIds: {{ $paginator->pluck('id')->toJson() }},
        toggleAll(checked) { this.selected = checked ? [...this.allIds] : []; },
        isAllSelected() { return this.allIds.length > 0 && this.selected.length === this.allIds.length; },
    }"
>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Tuyến đường</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $paginator->total() }} tuyến</p>
        </div>
        <a href="{{ route('admin.routes.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Thêm tuyến
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.routes.index') }}" class="flex flex-wrap items-end gap-3 bg-white border border-gray-200 rounded p-3">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input
                type="text"
                name="search"
                value="{{ $activeSearch }}"
                placeholder="Tên tuyến, tỉnh..."
                class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2.5 focus:outline-none focus:ring-1 focus:ring-amber-500"
            >
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tỉnh đầu</label>
            <select name="filter_province_start" class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <option value="">— Tất cả —</option>
                @foreach($provinces as $p)
                    <option value="{{ $p->id }}" @selected($filterProvinceStart == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tỉnh cuối</label>
            <select name="filter_province_end" class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <option value="">— Tất cả —</option>
                @foreach($provinces as $p)
                    <option value="{{ $p->id }}" @selected($filterProvinceEnd == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded border border-gray-300 transition-colors">Lọc</button>
        @if($activeSearch || $filterProvinceStart || $filterProvinceEnd)
        <a href="{{ route('admin.routes.index') }}" class="px-4 py-1.5 text-sm text-gray-500 hover:text-gray-700 border border-transparent rounded">Xóa lọc</a>
        @endif
    </form>

    {{-- Bulk bar --}}
    <div x-show="selected.length > 0" x-cloak class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded px-4 py-2 text-sm">
        <span class="text-amber-800 font-medium" x-text="`Đã chọn ${selected.length} tuyến`"></span>
        <form method="POST" action="{{ route('admin.routes.bulk-destroy') }}" onsubmit="return confirm('Xóa các tuyến đã chọn?')">
            @csrf
            @method('DELETE')
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded transition-colors">Xóa</button>
        </form>
    </div>

    {{-- Table --}}
    <x-admin::table.table>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 w-8">
                        <input type="checkbox" class="rounded border-gray-300"
                            :checked="isAllSelected()"
                            @change="toggleAll($event.target.checked)">
                    </th>
                    <th class="px-4 py-3 w-10"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Ảnh</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        @include('admin.routes._sort-link', ['key' => 'name', 'label' => 'Tên tuyến', 'activeSortKey' => $activeSortKey, 'activeSortDir' => $activeSortDir])
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tỉnh đầu → Tỉnh cuối</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        @include('admin.routes._sort-link', ['key' => 'price_default', 'label' => 'Giá mặc định', 'activeSortKey' => $activeSortKey, 'activeSortDir' => $activeSortDir])
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        @include('admin.routes._sort-link', ['key' => 'distance_km', 'label' => 'Km', 'activeSortKey' => $activeSortKey, 'activeSortDir' => $activeSortDir])
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Đón KS</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        @include('admin.routes._sort-link', ['key' => 'priority', 'label' => 'Ưu tiên', 'activeSortKey' => $activeSortKey, 'activeSortDir' => $activeSortDir])
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white" id="routes-sortable" data-sortable data-reorder-url="{{ route('admin.routes.reorder') }}">
                @forelse($paginator as $row)
                <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $row->id }}" data-sortable-id="{{ $row->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="rounded border-gray-300" :value="{{ $row->id }}" x-model="selected">
                    </td>
                    <td class="px-2 py-3 text-gray-400 cursor-grab active:cursor-grabbing" title="Kéo để sắp xếp" data-drag-handle>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 15h8"/></svg>
                    </td>
                    <td class="px-4 py-3">
                        @if($row->thumbnail_url)
                        <img src="{{ \App\Helpers\SystemHelper::mediaUrl($row->thumbnail_url) }}" alt="" class="w-10 h-10 rounded object-cover">
                        @else
                        <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.routes.edit', $row->id) }}" class="font-medium text-gray-900 hover:text-amber-600">{{ $row->name }}</a>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $row->slug }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $row->start_province_name }} → {{ $row->end_province_name }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">
                        @if($row->price_default)
                        {{ number_format($row->price_default) }}đ
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-gray-700">
                        {{ $row->distance_km ? $row->distance_km . ' km' : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($row->available_hotel_pickup)
                        <x-admin::display.badge color="success">Có</x-admin::display.badge>
                        @else
                        <x-admin::display.badge color="secondary">Không</x-admin::display.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-gray-500 text-xs" data-priority-value>{{ $row->priority }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.routes.edit', $row->id) }}" class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">Sửa</a>
                            <form method="POST" action="{{ route('admin.routes.destroy', $row->id) }}" onsubmit="return confirm('Xóa tuyến {{ addslashes($row->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs px-2 py-1 rounded border border-red-300 text-red-600 hover:bg-red-50 transition-colors">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-10 text-center text-gray-400 text-sm">Chưa có tuyến đường nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <x-admin::table.pagination :paginator="$paginator" />
    </x-admin::table.table>
</div>
@endsection
