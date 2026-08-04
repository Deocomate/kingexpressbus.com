@extends('admin.layouts.app')

@section('title', $block ? 'Sửa khóa chuyến' : 'Thêm khóa chuyến')

@section('breadcrumb')
    <a href="{{ route('admin.trips.index', ['section' => 'blocks']) }}" class="text-gray-500 hover:text-gray-700">Khóa chuyến</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700 font-medium">{{ $block ? 'Sửa' : 'Thêm' }}</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $block ? 'Sửa khóa chuyến' : 'Thêm khóa chuyến' }}</h1>
    </div>

    <form method="POST"
          action="{{ $block ? route('admin.trips.blocks.update', $block->id) : route('admin.trips.blocks.store') }}">
        @csrf
        @if($block) @method('PUT') @endif

        <x-admin::form.section title="Thông tin khóa chuyến">
            <div class="grid grid-cols-2 gap-5">

                {{-- Route select (not persisted — controls trip dependent select) --}}
                <div
                    x-data="{
                        routeId: '{{ old('route_id', $routeId) }}',
                        loading: false,
                        loadTrips(id) {
                            if (!id) return;
                            this.loading = true;
                            fetch('{{ route('admin.trips.api.trips-for-route') }}?route_id=' + id)
                                .then(r => r.json())
                                .then(data => {
                                    const sel = document.getElementById('field-trip_id');
                                    sel.innerHTML = '<option value=\'\'> Chọn chuyến </option>';
                                    data.results.forEach(opt => {
                                        const o = document.createElement('option');
                                        o.value = opt.id;
                                        o.textContent = opt.text;
                                        sel.appendChild(o);
                                    });
                                    sel.disabled = data.results.length === 0;
                                })
                                .finally(() => this.loading = false);
                        }
                    }"
                    x-init="routeId && loadTrips(routeId)"
                >
                    <label for="field-route_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Tuyến đường<span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <select
                        id="field-route_id"
                        name="route_id"
                        x-model="routeId"
                        x-on:change="loadTrips($event.target.value); document.getElementById('field-trip_id').value = ''"
                        required
                        class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
                    >
                        <option value="">— Chọn tuyến —</option>
                        @foreach($routes as $route)
                        <option value="{{ $route->id }}" @selected((string)old('route_id', $routeId) === (string)$route->id)>{{ $route->name }}</option>
                        @endforeach
                    </select>
                    <x-admin::form.field-error name="route_id" />
                </div>

                {{-- Trip select (dependent on route) --}}
                <div>
                    <label for="field-trip_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Chuyến xe<span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <select
                        id="field-trip_id"
                        name="trip_id"
                        required
                        @if(!$routeId && !old('route_id')) disabled @endif
                        class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 disabled:bg-gray-50 disabled:text-gray-400"
                    >
                        <option value="">— Chọn chuyến —</option>
                        @foreach($trips as $trip)
                        @php
                            $time  = substr((string)$trip->start_time, 0, 5);
                            $label = "$time - " . ($trip->bus?->name ?? 'N/A') . ' (' . ($trip->bus?->model_name ?? 'N/A') . ')';
                        @endphp
                        <option value="{{ $trip->id }}" @selected((string)old('trip_id', $block?->trip_id) === (string)$trip->id)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-admin::form.field-error name="trip_id" />
                </div>

                {{-- Date range --}}
                <x-admin::form.date
                    name="start_date"
                    label="Từ ngày"
                    :value="old('start_date', $block ? $block->start_date->format('d/m/Y') : '')"
                    mode="date"
                    required
                />
                <x-admin::form.date
                    name="end_date"
                    label="Đến ngày"
                    :value="old('end_date', $block ? $block->end_date->format('d/m/Y') : '')"
                    mode="date"
                    required
                />

                {{-- Block type --}}
                <div class="col-span-2">
                    <x-admin::form.radio-group
                        name="block_type"
                        label="Loại khóa"
                        :options="[
                            'off_day'  => 'Ngừng chạy (Tài xế nghỉ, lễ tết)',
                            'sold_out' => 'Khóa hết chỗ (Đã bao xe hoặc không nhận thêm khách)',
                        ]"
                        :value="old('block_type', $block?->block_type)"
                        required
                    />
                </div>

                {{-- Note --}}
                <div class="col-span-2">
                    <x-admin::form.textarea
                        name="note"
                        label="Ghi chú"
                        :value="old('note', $block?->note)"
                        rows="3"
                    />
                </div>
            </div>
        </x-admin::form.section>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium bg-brand-600 hover:bg-brand-700 text-white rounded shadow-sm transition-colors">
                {{ $block ? 'Lưu thay đổi' : 'Thêm khóa chuyến' }}
            </button>
            <a href="{{ route('admin.trips.index', ['section' => 'blocks']) }}"
               class="px-5 py-2 text-sm font-medium bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded shadow-sm transition-colors">
                Hủy
            </a>
            @if($block)
            <form method="POST" action="{{ route('admin.trips.blocks.destroy', $block->id) }}"
                  class="ml-auto" onsubmit="return confirm('Xóa khóa chuyến này?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2 text-sm font-medium text-red-600 hover:text-red-800">Xóa</button>
            </form>
            @endif
        </div>
    </form>
</div>
@endsection
