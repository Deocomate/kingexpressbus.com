<form id="bulk-form-provinces" method="POST" action="{{ route('admin.locations.provinces.bulk-destroy') }}">
    @csrf
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        :formAction="route('admin.locations.provinces.bulk-destroy')"
    />

    <x-admin::table.table id="provinces-table">
        @include('admin.locations._table-provinces-rows', compact('paginator'))
    </x-admin::table.table>
</form>
