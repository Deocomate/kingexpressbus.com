{{-- Swappable table body: this is exactly what table.js fetches and swaps via
     outerHTML on pagination/filter/per-page changes (X-Partial: table header). --}}
<x-admin::table.table id="blocks-table">
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        formAction="{{ route('admin.trips.blocks.bulk-destroy') }}"
    />

    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-8 px-4 py-3">
                    <input type="checkbox" data-select-all class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tuyến đường</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Chuyến xe</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Từ ngày</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Đến ngày</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Loại khóa</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Ghi chú</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($paginator as $block)
            @php
                $typeLabel = match($block->block_type) {
                    'off_day'  => 'Ngừng chạy',
                    'sold_out' => 'Hết chỗ',
                    default    => $block->block_type,
                };
                $typeColor = match($block->block_type) {
                    'off_day'  => 'warning',
                    'sold_out' => 'danger',
                    default    => 'secondary',
                };
                $tripLabel = substr((string)$block->trip_start_time, 0, 5) . ' - ' . ($block->bus_name ?? 'N/A') . ' (' . ($block->bus_model ?? 'N/A') . ')';
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <input type="checkbox" name="ids[]" value="{{ $block->id }}"
                           data-row-check class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                </td>
                <td class="px-4 py-3 text-gray-700">{{ $block->route_name }}</td>
                <td class="px-4 py-3 text-gray-600 font-mono">{{ $tripLabel }}</td>
                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($block->start_date)->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($block->end_date)->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <x-admin::display.badge :color="$typeColor">{{ $typeLabel }}</x-admin::display.badge>
                </td>
                <td class="px-4 py-3 text-gray-500 max-w-[200px] truncate" title="{{ $block->note }}">{{ $block->note }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.trips.blocks.edit', $block->id) }}"
                           class="text-xs text-brand-600 hover:text-brand-800 font-medium">Sửa</a>
                        <form method="POST" action="{{ route('admin.trips.blocks.destroy', $block->id) }}"
                              onsubmit="return confirm('Xóa khóa chuyến này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400">Không có khóa chuyến nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <x-admin::table.pagination :paginator="$paginator" />
</x-admin::table.table>
