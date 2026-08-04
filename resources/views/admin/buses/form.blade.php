@extends('admin.layouts.app')

@php
    $pageTitle = $isEdit ? 'Sửa xe' : 'Thêm xe';
    $formAction = $isEdit
        ? route('admin.buses.update', $bus->id)
        : route('admin.buses.store');
    $selectedServiceIds = $isEdit
        ? $bus->services->pluck('id')->map(fn($id) => (string) $id)->all()
        : (array) old('services', []);
@endphp

@section('title', $pageTitle)
@section('breadcrumb')
    <a href="{{ route('admin.buses.index') }}" class="hover:text-gray-700 transition-colors">Đội xe</a>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0 text-gray-400"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
    <span class="text-gray-700 font-medium">{{ $pageTitle }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">{{ $pageTitle }}</h1>
        <div class="flex items-center gap-3">
            @if($isEdit)
            <form method="POST" action="{{ route('admin.buses.destroy', $bus->id) }}" onsubmit="return confirm('Xóa xe {{ addslashes($bus->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50 transition-colors">Xóa</button>
            </form>
            @endif
            <a href="{{ route('admin.buses.index') }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded hover:bg-gray-50 transition-colors">Hủy</a>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-3 gap-6">
            {{-- Main --}}
            <div class="col-span-2 space-y-5">
                <x-admin::form.section title="Thông tin xe">
                    <x-admin::form.input
                        name="name"
                        label="Tên xe"
                        :value="old('name', $bus?->name)"
                        required
                        placeholder="VD: Xe Giường Nằm 36 chỗ"
                    />

                    <x-admin::form.input
                        name="model_name"
                        label="Dòng xe / Model"
                        :value="old('model_name', $bus?->model_name)"
                        placeholder="VD: Hyundai Universe, Thaco Mobihome"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <x-admin::form.input
                            name="seat_count"
                            label="Số ghế / giường"
                            type="number"
                            :value="old('seat_count', $bus?->seat_count ?? 36)"
                            required
                            hint="Tối thiểu 1"
                        />
                        <x-admin::form.input
                            name="priority"
                            label="Ưu tiên hiển thị"
                            type="number"
                            :value="old('priority', $bus?->priority ?? 0)"
                            hint="Số lớn hơn hiển thị trước"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dịch vụ</label>
                        <select
                            name="services[]"
                            multiple
                            data-select-search
                            class="block w-full text-sm"
                        >
                            @foreach($allServices as $svc)
                            <option value="{{ $svc->id }}" @selected(in_array((string)$svc->id, $selectedServiceIds))>{{ $svc->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Chọn các dịch vụ trang bị trên xe này</p>
                        <x-admin::form.field-error name="services" />
                    </div>
                </x-admin::form.section>

                <x-admin::form.section title="Hình ảnh & Nội dung">
                    <x-admin::form.upload
                        name="thumbnail_url"
                        label="Ảnh đại diện"
                        :value="old('thumbnail_url', $bus?->thumbnail_url)"
                    />

                    <x-admin::form.upload
                        name="image_list_url[]"
                        label="Album ảnh (tối đa 10)"
                        :value="old('image_list_url', $bus?->image_list_url ?? [])"
                        multiple
                        hint="Tối đa 10 ảnh, kéo để sắp xếp"
                    />

                    <x-admin::form.rich-text
                        name="content"
                        label="Mô tả chi tiết"
                        :value="old('content', $bus?->content)"
                        height="300px"
                    />
                </x-admin::form.section>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                <x-admin::form.section>
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md shadow-sm transition-colors">
                        {{ $isEdit ? 'Lưu thay đổi' : 'Tạo xe' }}
                    </button>
                    @if($isEdit)
                    <a href="{{ route('admin.buses.create') }}" class="block text-center text-xs text-gray-500 hover:text-gray-700 mt-2">+ Thêm xe mới</a>
                    @endif
                </x-admin::form.section>

                @if($isEdit)
                <div class="bg-white rounded border border-gray-200 shadow-sm p-4 text-xs text-gray-500 space-y-1">
                    <p>Tạo: {{ $bus->created_at?->format('d/m/Y H:i') }}</p>
                    <p>Sửa: {{ $bus->updated_at?->format('d/m/Y H:i') }}</p>
                    <p>ID: {{ $bus->id }}</p>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
