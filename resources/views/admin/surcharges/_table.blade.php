{{-- Search --}}
<form method="GET" action="{{ route('admin.surcharges.index') }}" class="flex gap-3 items-end px-1 py-3" data-table-filter>
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
        @include('admin.surcharges._table-rows', compact('paginator'))
    </x-admin::table.table>
</form>
