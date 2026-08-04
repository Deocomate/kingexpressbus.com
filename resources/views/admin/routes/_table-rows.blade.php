{{--
  Swappable fragment: table rows + pagination only (no outer [data-table] wrapper).
  Rendered directly by RouteController for X-Partial requests, and included
  from index.blade.php for the normal full-page render.
--}}
<table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3 w-8">
                <input type="checkbox" data-select-all class="rounded border-gray-300">
            </th>
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
    <tbody
        class="divide-y divide-gray-100 bg-white"
        id="routes-sortable"
        data-sortable
        data-reorder-url="{{ route('admin.routes.reorder') }}"
    >
        @forelse($paginator as $row)
        <tr class="hover:bg-gray-50 transition-colors" data-sortable-id="{{ $row->id }}" data-drag-handle>
            <td class="px-4 py-3">
                <input type="checkbox" name="ids[]" value="{{ $row->id }}" data-row-check class="rounded border-gray-300">
            </td>
            <td class="px-4 py-3">
                @if($row->thumbnail_url)
                <img src="{{ Storage::url($row->thumbnail_url) }}" alt="" class="w-10 h-10 rounded object-cover">
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
            <td class="px-4 py-3 text-right text-gray-500 text-xs">{{ $row->priority }}</td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.routes.edit', $row->id) }}" class="text-xs text-gray-500 hover:text-amber-600 transition-colors">Sửa</a>
                    <form method="POST" action="{{ route('admin.routes.destroy', $row->id) }}" onsubmit="return confirm('Xóa tuyến {{ addslashes($row->name) }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 transition-colors">Xóa</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="px-4 py-10 text-center text-gray-400 text-sm">Chưa có tuyến đường nào.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<x-admin::table.pagination :paginator="$paginator" />
