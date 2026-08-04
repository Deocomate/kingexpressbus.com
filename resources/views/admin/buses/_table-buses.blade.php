{{-- Buses table sub-view --}}
<div class="space-y-4">
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
    <form method="GET" action="{{ route('admin.buses.index') }}" class="flex items-end gap-3 bg-white border border-gray-200 rounded p-3" data-table-filter>
        <input type="hidden" name="section" value="buses">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ $activeSearch }}" placeholder="Tên xe, dòng xe..."
                class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2.5 focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
        <button type="submit" class="px-4 py-1.5 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded border border-gray-300 transition-colors">Lọc</button>
    </form>

    <form id="bulk-form-buses" method="POST" action="{{ route('admin.buses.bulk-destroy') }}">
        @csrf
        <x-admin::table.bulk-bar
            :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
            formAction="{{ route('admin.buses.bulk-destroy') }}"
        />

    <x-admin::table.table>
        @include('admin.buses._table-buses-rows', compact('paginator', 'busServices'))
    </x-admin::table.table>
    </form>
</div>
