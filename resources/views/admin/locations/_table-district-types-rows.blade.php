{{--
  Swappable fragment: table rows + pagination only (no outer [data-table] wrapper).
  Rendered directly by LocationController for X-Partial requests, and included
  from _table-district-types.blade.php for the normal full-page render.
--}}
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="w-10 px-4 py-3">
                <input type="checkbox" data-select-all class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            </th>
            <th class="px-4 py-3 w-10"></th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên loại</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Ưu tiên</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Thao tác</th>
        </tr>
    </thead>
    <tbody
        class="bg-white divide-y divide-gray-200"
        id="district-types-sortable"
        data-sortable
        data-reorder-url="{{ route('admin.locations.district-types.reorder') }}"
    >
        @forelse($paginator as $dt)
        <tr class="hover:bg-gray-50 transition-colors" data-sortable-id="{{ $dt->id }}">
            <td class="px-4 py-3">
                <input type="checkbox" name="ids[]" value="{{ $dt->id }}" data-row-check class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
            </td>
            <td class="px-2 py-3 text-gray-400 cursor-grab active:cursor-grabbing" title="Kéo để sắp xếp" data-drag-handle>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 15h8"/></svg>
            </td>
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $dt->name }}</td>
            <td class="px-4 py-3 text-sm text-gray-600 text-center" data-priority-value>{{ $dt->priority }}</td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <button type="button"
                            @click="$dispatch('open-slide-over', { id: 'dt-edit-over-{{ $dt->id }}' })"
                            class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">Sửa</button>
                    <form method="POST" action="{{ route('admin.locations.district-types.destroy', $dt) }}"
                          onsubmit="return confirm('Xóa loại địa điểm này?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs px-2 py-1 rounded border border-red-300 text-red-600 hover:bg-red-50 transition-colors">Xóa</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Chưa có dữ liệu.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<x-admin::table.pagination :paginator="$paginator" />

{{-- Inline edit slide-overs for each row (generated per item) --}}
@foreach($paginator as $dt)
<x-admin::display.slide-over :id="'dt-edit-over-' . $dt->id" title="Sửa loại địa điểm">
    <form method="POST" action="{{ route('admin.locations.district-types.update', $dt) }}" class="space-y-5">
        @csrf @method('PUT')
        <x-admin::form.input name="name" label="Tên loại địa điểm" :value="$dt->name" required />
        <x-admin::form.input name="priority" label="Độ ưu tiên" type="number" :value="$dt->priority" required />
        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">Lưu</button>
            <button type="button" @click="open = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">Hủy</button>
        </div>
    </form>
</x-admin::display.slide-over>
@endforeach
