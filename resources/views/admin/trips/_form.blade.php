@extends('admin.layouts.app')

@section('title', $trip ? 'Sửa chuyến xe' : 'Thêm chuyến xe')

@section('breadcrumb')
    <a href="{{ route('admin.trips.index') }}" class="text-gray-500 hover:text-gray-700">Chuyến xe</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700 font-medium">{{ $trip ? 'Sửa' : 'Thêm' }}</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $trip ? 'Sửa chuyến xe' : 'Thêm chuyến xe' }}</h1>
    </div>

    <form method="POST"
          action="{{ $trip ? route('admin.trips.update', $trip->id) : route('admin.trips.store') }}">
        @csrf
        @if($trip) @method('PUT') @endif

        <x-admin::form.section title="Lịch chạy">
            <div class="grid grid-cols-2 gap-5">
                <x-admin::form.select
                    name="route_id"
                    label="Tuyến đường"
                    :options="$routes->pluck('name','id')"
                    :value="old('route_id', $trip?->route_id)"
                    required
                />
                <x-admin::form.select
                    name="bus_id"
                    label="Xe"
                    :options="$buses->pluck('name','id')"
                    :value="old('bus_id', $trip?->bus_id)"
                    required
                />
                <x-admin::form.input
                    name="start_time"
                    label="Giờ xuất bến"
                    type="time"
                    :value="old('start_time', $trip ? substr($trip->start_time, 0, 5) : '')"
                    required
                />
                <x-admin::form.input
                    name="end_time"
                    label="Giờ đến"
                    type="time"
                    :value="old('end_time', $trip ? substr($trip->end_time, 0, 5) : '')"
                    hint="Được phép qua đêm (end_time < start_time)"
                    required
                />
                <x-admin::form.money
                    name="price"
                    label="Giá vé"
                    :value="old('price', $trip?->price ?? 0)"
                    required
                />
                <x-admin::form.input
                    name="priority"
                    label="Độ ưu tiên"
                    type="number"
                    :value="old('priority', $trip?->priority ?? 0)"
                    required
                />
                <div class="col-span-2">
                    <x-admin::form.toggle
                        name="is_active"
                        label="Đang hoạt động"
                        :value="old('is_active', $trip?->is_active ?? true)"
                    />
                </div>
            </div>
        </x-admin::form.section>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium bg-brand-600 hover:bg-brand-700 text-white rounded shadow-sm transition-colors">
                {{ $trip ? 'Lưu thay đổi' : 'Thêm chuyến xe' }}
            </button>
            <a href="{{ route('admin.trips.index') }}"
               class="px-5 py-2 text-sm font-medium bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded shadow-sm transition-colors">
                Hủy
            </a>
            @if($trip)
            <form method="POST" action="{{ route('admin.trips.destroy', $trip->id) }}"
                  class="ml-auto" onsubmit="return confirm('Xóa chuyến xe này?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2 text-sm font-medium text-red-600 hover:text-red-800">Xóa</button>
            </form>
            @endif
        </div>
    </form>
</div>
@endsection
