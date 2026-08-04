{{-- Search --}}
<form method="GET" action="{{ route('admin.surcharges.index') }}" class="flex gap-3 items-end px-1 py-3">
    <div class="flex-1 max-w-xs">
        <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Tên phụ thu, lý do…"
               class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <button type="submit" class="px-4 py-2 text-sm font-medium bg-gray-700 hover:bg-gray-800 text-white rounded shadow-sm transition-colors">Tìm</button>
    <a href="{{ route('admin.surcharges.index') }}"
       class="px-4 py-2 text-sm font-medium bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded shadow-sm transition-colors">Xóa</a>
</form>

{{-- Bulk --}}
<form id="bulk-form-surcharges" method="POST" action="{{ route('admin.surcharges.bulk-destroy') }}">
    @csrf @method('DELETE')
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        formAction="{{ route('admin.surcharges.bulk-destroy') }}"
    />

    <x-admin::table.table id="surcharges-table">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-8 px-4 py-3">
                        <input type="checkbox" data-select-all class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tên</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Từ ngày</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Đến ngày</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Phụ thu chung</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">Áp dụng</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Ưu tiên</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($paginator as $surcharge)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="ids[]" value="{{ $surcharge->id }}"
                               data-row-check class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $surcharge->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $surcharge->start_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $surcharge->end_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($surcharge->global_surcharge_amount) }}đ</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-center">
                            @if($surcharge->is_active)
                            <x-admin::display.badge color="success">Đang áp dụng</x-admin::display.badge>
                            @else
                            <x-admin::display.badge color="secondary">Tắt</x-admin::display.badge>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-500">{{ $surcharge->priority }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.surcharges.edit', $surcharge->id) }}"
                               class="text-xs text-brand-600 hover:text-brand-800 font-medium">Sửa</a>
                            <form method="POST" action="{{ route('admin.surcharges.destroy', $surcharge->id) }}"
                                  onsubmit="return confirm('Xóa phụ thu này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400">Không có phụ thu nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <x-admin::table.pagination :paginator="$paginator" />
    </x-admin::table.table>
</form>
