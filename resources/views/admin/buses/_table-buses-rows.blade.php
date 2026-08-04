{{--
  Swappable fragment: table rows + pagination only (no outer [data-table] wrapper).
  Rendered directly by BusController for X-Partial requests, and included
  from _table-buses.blade.php for the normal full-page render.
--}}
<table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3 w-8">
                <input type="checkbox" data-select-all class="rounded border-gray-300">
            </th>
            <th class="px-4 py-3 w-10"></th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">Ảnh</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên xe</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dòng xe</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ghế</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dịch vụ</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ưu tiên</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
        </tr>
    </thead>
    <tbody
        class="divide-y divide-gray-100 bg-white"
        id="buses-sortable"
        data-sortable
        data-reorder-url="{{ route('admin.buses.reorder') }}"
    >
        @forelse($paginator as $row)
        <tr class="hover:bg-gray-50 transition-colors" data-sortable-id="{{ $row->id }}">
            <td class="px-4 py-3">
                <input type="checkbox" name="ids[]" value="{{ $row->id }}" data-row-check class="rounded border-gray-300">
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
                <a href="{{ route('admin.buses.edit', $row->id) }}" class="font-medium text-gray-900 hover:text-amber-600">{{ $row->name }}</a>
            </td>
            <td class="px-4 py-3 text-gray-600">{{ $row->model_name ?? '—' }}</td>
            <td class="px-4 py-3 text-right text-gray-700">{{ $row->seat_count }}</td>
            <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                    @foreach($busServices[$row->id]?->services ?? [] as $svc)
                    <x-admin::display.badge color="info">{{ $svc->name }}</x-admin::display.badge>
                    @endforeach
                </div>
            </td>
            <td class="px-4 py-3 text-right text-gray-500 text-xs" data-priority-value>{{ $row->priority }}</td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.buses.edit', $row->id) }}" class="text-xs text-gray-500 hover:text-amber-600 transition-colors">Sửa</a>
                    <form method="POST" action="{{ route('admin.buses.destroy', $row->id) }}" onsubmit="return confirm('Xóa xe {{ addslashes($row->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 transition-colors">Xóa</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="px-4 py-10 text-center text-gray-400 text-sm">Chưa có xe nào.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<x-admin::table.pagination :paginator="$paginator" />
