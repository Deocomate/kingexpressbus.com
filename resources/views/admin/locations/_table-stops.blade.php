<form id="bulk-form-stops" method="POST" action="{{ route('admin.locations.stops.bulk-destroy') }}">
    @csrf
    <x-admin::table.bulk-bar
        :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
        :formAction="route('admin.locations.stops.bulk-destroy')"
    />

    <x-admin::table.table id="stops-table">
        @include('admin.locations._table-stops-rows', compact('paginator'))
    </x-admin::table.table>
</form>
