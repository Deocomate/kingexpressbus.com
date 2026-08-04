{{--
  Swappable fragment: table rows + pagination only (no outer [data-table] wrapper).
  Rendered directly by BusController for X-Partial requests, and included
  from _table-services.blade.php for the normal full-page render.
--}}
<table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên dịch vụ</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Số xe gắn</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
        @forelse($servicesPaginator as $svc)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 font-medium text-gray-900">{{ $svc->name }}</td>
            <td class="px-4 py-3 text-gray-600 text-xs font-mono">{{ $svc->icon ?: '—' }}</td>
            <td class="px-4 py-3 text-right text-gray-700">{{ $svc->bus_count }}</td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button type="button"
                        @click="openEdit({{ $svc->id }}, {{ Js::from($svc->name) }}, {{ Js::from($svc->icon) }})"
                        class="text-xs text-gray-500 hover:text-amber-600 transition-colors">Sửa</button>
                    <button type="button"
                        @click="deleteService({{ $svc->id }}, {{ Js::from($svc->name) }})"
                        class="text-xs text-gray-400 hover:text-red-600 transition-colors">Xóa</button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">Chưa có dịch vụ nào.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<x-admin::table.pagination :paginator="$servicesPaginator" />
