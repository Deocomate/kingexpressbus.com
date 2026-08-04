<form id="bulk-form-district-types" method="POST" action="{{ route('admin.locations.district-types.bulk-destroy') }}">
    @csrf
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        :formAction="route('admin.locations.district-types.bulk-destroy')"
    />

    <x-admin::table.table id="district-types-table">
        @include('admin.locations._table-district-types-rows', compact('paginator'))
    </x-admin::table.table>
</form>
