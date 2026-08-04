@extends('admin.layouts.app')

@section('title', $surcharge ? 'Sửa phụ thu' : 'Thêm phụ thu')

@section('breadcrumb')
    <a href="{{ route('admin.surcharges.index') }}" class="text-gray-500 hover:text-gray-700">Phụ thu</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700 font-medium">{{ $surcharge ? 'Sửa' : 'Thêm' }}</span>
@endsection

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $surcharge ? 'Sửa phụ thu' : 'Thêm phụ thu' }}</h1>
    </div>

    <form method="POST"
          action="{{ $surcharge ? route('admin.surcharges.update', $surcharge->id) : route('admin.surcharges.store') }}">
        @csrf
        @if($surcharge) @method('PUT') @endif

        <div class="space-y-5">
            {{-- Main config --}}
            <x-admin::form.section title="Cấu hình phụ thu">
                <div class="grid grid-cols-2 gap-5">
                    <div class="col-span-2">
                        <x-admin::form.input
                            name="name"
                            label="Tên phụ thu"
                            :value="old('name', $surcharge?->name)"
                            required
                        />
                    </div>
                    <x-admin::form.date
                        name="start_date"
                        label="Ngày bắt đầu"
                        :value="old('start_date', $surcharge?->start_date ? \Carbon\Carbon::parse($surcharge->start_date)->format('d/m/Y') : '')"
                        required
                    />
                    <x-admin::form.date
                        name="end_date"
                        label="Ngày kết thúc"
                        :value="old('end_date', $surcharge?->end_date ? \Carbon\Carbon::parse($surcharge->end_date)->format('d/m/Y') : '')"
                        required
                    />
                    <x-admin::form.money
                        name="global_surcharge_amount"
                        label="Phụ thu chung (áp dụng mọi tuyến)"
                        :value="old('global_surcharge_amount', $surcharge?->global_surcharge_amount ?? 0)"
                        required
                    />
                    <x-admin::form.input
                        name="priority"
                        label="Độ ưu tiên"
                        type="number"
                        :value="old('priority', $surcharge?->priority ?? 0)"
                        required
                    />
                    <div class="col-span-2">
                        <x-admin::form.textarea
                            name="reason"
                            label="Lý do"
                            :value="old('reason', $surcharge?->reason)"
                            rows="2"
                        />
                    </div>
                    <div class="col-span-2">
                        <x-admin::form.toggle
                            name="is_active"
                            label="Đang áp dụng"
                            :value="old('is_active', $surcharge?->is_active ?? true)"
                        />
                    </div>
                </div>
            </x-admin::form.section>

            {{-- Route adjustments (dynamic rows via Alpine) --}}
            <x-admin::form.section title="Ngoại lệ theo tuyến" description="Phụ thu bổ sung cho từng tuyến (cộng dồn với phụ thu chung)">
                <div
                    x-data="{
                        rows: @js(collect(old('route_adjustments', $surcharge?->route_adjustments ?? []))->map(fn($r) => ['route_id' => (string)$r['route_id'], 'route_surcharge_amount' => (string)$r['route_surcharge_amount']])->values()->all()),
                        addRow() { this.rows.push({ route_id: '', route_surcharge_amount: '0' }); },
                        removeRow(i) { this.rows.splice(i, 1); }
                    }"
                    class="space-y-3"
                >
                    <template x-for="(row, i) in rows" :key="i">
                        <div class="flex items-end gap-3 p-3 bg-gray-50 rounded border border-gray-200">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Tuyến đường</label>
                                <select :name="'route_adjustments[' + i + '][route_id]'"
                                        x-model="row.route_id"
                                        class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500">
                                    <option value="">— Chọn tuyến —</option>
                                    @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-48">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Phụ thu tuyến (đ)</label>
                                <input type="text"
                                       :name="'route_adjustments[' + i + '][route_surcharge_amount]'"
                                       x-model="row.route_surcharge_amount"
                                       inputmode="numeric"
                                       class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 text-right">
                            </div>
                            <button type="button" @click="removeRow(i)"
                                    class="pb-2 text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="addRow()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-brand-700 bg-brand-50 border border-brand-200 hover:bg-brand-100 rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Thêm tuyến
                    </button>
                </div>
            </x-admin::form.section>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium bg-brand-600 hover:bg-brand-700 text-white rounded shadow-sm transition-colors">
                {{ $surcharge ? 'Lưu thay đổi' : 'Thêm phụ thu' }}
            </button>
            <a href="{{ route('admin.surcharges.index') }}"
               class="px-5 py-2 text-sm font-medium bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded shadow-sm transition-colors">
                Hủy
            </a>
            @if($surcharge)
            <form method="POST" action="{{ route('admin.surcharges.destroy', $surcharge->id) }}"
                  class="ml-auto" onsubmit="return confirm('Xóa phụ thu này?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2 text-sm font-medium text-red-600 hover:text-red-800">Xóa</button>
            </form>
            @endif
        </div>
    </form>
</div>
@endsection
