<x-admin::table.table id="districts-table">
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        :formAction="route('admin.locations.districts.bulk-destroy')"
    />

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-10 px-4 py-3">
                    <input type="checkbox" data-select-all class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                </th>
                <th class="px-4 py-3 w-10"></th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-14">Ảnh</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Tỉnh/thành</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Loại</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Đường dẫn</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Ưu tiên</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="districts-sortable">
            @forelse($paginator as $district)
            <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $district->id }}">
                <td class="px-4 py-3">
                    <input type="checkbox" data-row-checkbox value="{{ $district->id }}" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                </td>
                <td class="px-2 py-3 text-gray-400 cursor-grab active:cursor-grabbing" title="Kéo để sắp xếp">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 15h8"/></svg>
                </td>
                <td class="px-4 py-3">
                    @if($district->thumbnail_url)
                    <img src="{{ asset('storage/' . $district->thumbnail_url) }}"
                         alt="{{ $district->name }}"
                         class="w-10 h-10 object-cover rounded">
                    @else
                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                    </div>
                    @endif
                </td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $district->name }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 hidden md:table-cell">{{ $district->province?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $district->districtType?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 font-mono text-xs hidden lg:table-cell">{{ $district->slug }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $district->priority }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.locations.districts.edit', $district) }}"
                           class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">Sửa</a>
                        <form method="POST" action="{{ route('admin.locations.districts.destroy', $district) }}"
                              onsubmit="return confirm('Xóa địa điểm này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs px-2 py-1 rounded border border-red-300 text-red-600 hover:bg-red-50 transition-colors">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">Chưa có dữ liệu.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-admin::table.pagination :paginator="$paginator" />
</x-admin::table.table>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('districts-sortable');
    if (!el || typeof Sortable === 'undefined') return;
    Sortable.create(el, {
        animation: 150,
        handle: 'td:nth-child(2)',
        onEnd: function () {
            var ids = Array.from(el.querySelectorAll('tr[data-id]')).map(function (r) { return r.dataset.id; });
            fetch('{{ route('admin.locations.districts.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ ids: ids }),
            });
        },
    });
});
</script>
