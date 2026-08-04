{{-- Swappable table body: this is exactly what table.js fetches and swaps via
     outerHTML on pagination/filter/per-page changes (X-Partial: table header). --}}
<x-admin::table.table id="trips-table">
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        formAction="{{ route('admin.trips.bulk-destroy') }}"
    />

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
