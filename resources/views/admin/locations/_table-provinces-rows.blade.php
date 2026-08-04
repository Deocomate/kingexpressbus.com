{{--
  Swappable fragment: table rows + pagination only (no outer [data-table] wrapper).
  Rendered directly by LocationController for X-Partial requests, and included
  from _table-provinces.blade.php for the normal full-page render.
--}}
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="w-10 px-4 py-3">
                <input type="checkbox" data-select-all class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            </th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10"></th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-14">Ảnh</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đường dẫn</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell w-24">Số địa điểm</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Tiêu đề SEO</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Ưu tiên</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Thao tác</th>
        </tr>
    </thead>
    <tbody
        class="bg-white divide-y divide-gray-200"
        id="provinces-sortable"
        data-sortable
        data-reorder-url="{{ route('admin.locations.provinces.reorder') }}"
    >
        @forelse($paginator as $province)
        <tr class="hover:bg-gray-50 transition-colors" data-sortable-id="{{ $province->id }}">
            <td class="px-4 py-3">
                <input type="checkbox" name="ids[]" value="{{ $province->id }}" data-row-check class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            </td>
            <td class="px-2 py-3 text-gray-400 cursor-grab active:cursor-grabbing" title="Kéo để sắp xếp" data-drag-handle>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 15h8"/></svg>
            </td>
            <td class="px-4 py-3">
                @if($province->thumbnail_url)
                <img src="{{ asset('storage/' . $province->thumbnail_url) }}"
                     alt="{{ $province->name }}"
                     class="w-10 h-10 object-cover rounded">
                @else
                <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                </div>
                @endif
            </td>
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $province->name }}</td>
            <td class="px-4 py-3 text-sm text-gray-600 font-mono text-xs">{{ $province->slug }}</td>
            <td class="px-4 py-3 text-sm text-gray-600 text-center hidden md:table-cell">{{ $province->districts_count }}</td>
            <td class="px-4 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $province->title ?? '—' }}</td>
            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $province->priority }}</td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.locations.provinces.edit', $province) }}"
                       class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">Sửa</a>
                    <form method="POST" action="{{ route('admin.locations.provinces.destroy', $province) }}"
                          onsubmit="return confirm('Xóa tỉnh/thành này?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs px-2 py-1 rounded border border-red-300 text-red-600 hover:bg-red-50 transition-colors">Xóa</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Chưa có dữ liệu.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<x-admin::table.pagination :paginator="$paginator" />
