{{--
  Swappable fragment: table rows + pagination only (no outer [data-table] wrapper).
  Rendered directly by HolidaySurchargeController for X-Partial requests, and
  included from _table.blade.php for the normal full-page render.
--}}
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
