{{-- Buses table sub-view --}}
<div
    class="space-y-4"
    x-data="{
        selected: [],
        allIds: {{ $paginator->pluck('id')->toJson() }},
        toggleAll(checked) { this.selected = checked ? [...this.allIds] : []; },
        isAllSelected() { return this.allIds.length > 0 && this.selected.length === this.allIds.length; },
    }"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Xe khách</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $paginator->total() }} xe</p>
        </div>
        <a href="{{ route('admin.buses.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Thêm xe
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.buses.index') }}" class="flex items-end gap-3 bg-white border border-gray-200 rounded p-3">
        <input type="hidden" name="section" value="buses">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ $activeSearch }}" placeholder="Tên xe, dòng xe..."
                class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2.5 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <button type="submit" class="px-4 py-1.5 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded border border-gray-300 transition-colors">Lọc</button>
    </form>

    {{-- Bulk bar --}}
    <div x-show="selected.length > 0" x-cloak class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded px-4 py-2 text-sm">
        <span class="text-amber-800 font-medium" x-text="`Đã chọn ${selected.length} xe`"></span>
        <form method="POST" action="{{ route('admin.buses.bulk-destroy') }}" onsubmit="return confirm('Xóa các xe đã chọn?')">
            @csrf @method('DELETE')
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 rounded transition-colors">Xóa</button>
        </form>
    </div>

    <x-admin::table.table>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 w-8">
                        <input type="checkbox" class="rounded border-gray-300"
                            :checked="isAllSelected()"
                            @change="toggleAll($event.target.checked)">
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
            <tbody class="divide-y divide-gray-100 bg-white" id="buses-sortable" data-sortable data-reorder-url="{{ route('admin.buses.reorder') }}">
                @forelse($paginator as $row)
                <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $row->id }}" data-sortable-id="{{ $row->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="rounded border-gray-300" :value="{{ $row->id }}" x-model="selected">
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
                            <a href="{{ route('admin.buses.edit', $row->id) }}" class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">Sửa</a>
                            <form method="POST" action="{{ route('admin.buses.destroy', $row->id) }}" onsubmit="return confirm('Xóa xe {{ addslashes($row->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs px-2 py-1 rounded border border-red-300 text-red-600 hover:bg-red-50 transition-colors">Xóa</button>
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
    </x-admin::table.table>
</div>
