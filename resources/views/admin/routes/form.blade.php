@extends('admin.layouts.app')

@php
    $pageTitle = $isEdit ? 'Sửa tuyến đường' : 'Thêm tuyến đường';
    $formAction = $isEdit
        ? route('admin.routes.update', $route->id)
        : route('admin.routes.store');
@endphp

@section('title', $pageTitle)
@section('breadcrumb')
    <a href="{{ route('admin.routes.index') }}" class="hover:text-gray-700 transition-colors">Tuyến đường</a>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0 text-gray-400"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
    <span class="text-gray-700 font-medium">{{ $pageTitle }}</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header row --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">{{ $pageTitle }}</h1>
        <div class="flex items-center gap-3">
            @if($isEdit)
            <form method="POST" action="{{ route('admin.routes.destroy', $route->id) }}" onsubmit="return confirm('Xóa tuyến {{ addslashes($route->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50 transition-colors">Xóa</button>
            </form>
            @endif
            <a href="{{ route('admin.routes.index') }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded hover:bg-gray-50 transition-colors">Hủy</a>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-3 gap-6">
            {{-- Main column --}}
            <div class="col-span-2 space-y-5">
                <x-admin::form.section title="Thông tin tuyến đường">
                    <div class="grid grid-cols-2 gap-4">
                        <x-admin::form.select-search
                            name="province_start_id"
                            label="Tỉnh đầu"
                            source="provinces"
                            :value="old('province_start_id', $route?->province_start_id)"
                            :valueText="old('province_start_id') ? null : $route?->startProvince?->name"
                            required
                        />
                        <x-admin::form.select-search
                            name="province_end_id"
                            label="Tỉnh cuối"
                            source="provinces"
                            :value="old('province_end_id', $route?->province_end_id)"
                            :valueText="old('province_end_id') ? null : $route?->endProvince?->name"
                            required
                        />
                    </div>

                    <x-admin::form.input
                        name="name"
                        label="Tên tuyến"
                        :value="old('name', $route?->name)"
                        required
                        data-slug-source
                        placeholder="VD: Hà Nội - Đà Nẵng"
                    />

                    <x-admin::form.input
                        name="slug"
                        label="Slug (URL)"
                        :value="old('slug', $route?->slug)"
                        required
                        data-slug-target
                        placeholder="ha-noi-da-nang"
                        hint="Chỉ chữ thường, số và dấu gạch ngang"
                    />

                    <x-admin::form.input
                        name="title"
                        label="Tiêu đề SEO"
                        :value="old('title', $route?->title)"
                        placeholder="Tiêu đề hiển thị trên tab trình duyệt"
                    />

                    <x-admin::form.textarea
                        name="description"
                        label="Mô tả SEO"
                        :value="old('description', $route?->description)"
                        rows="3"
                        placeholder="Mô tả ngắn cho SEO"
                    />

                    <div class="grid grid-cols-3 gap-4">
                        <x-admin::form.input
                            name="duration"
                            label="Thời gian"
                            :value="old('duration', $route?->duration)"
                            placeholder="VD: 8-10 giờ"
                        />
                        <x-admin::form.input
                            name="distance_km"
                            label="Quãng đường (km)"
                            type="number"
                            :value="old('distance_km', $route?->distance_km)"
                            placeholder="800"
                        />
                        <x-admin::form.money
                            name="price_default"
                            label="Giá mặc định"
                            :value="old('price_default', $route?->price_default)"
                        />
                    </div>

                    <x-admin::form.toggle
                        name="available_hotel_pickup"
                        label="Đón tại khách sạn"
                        :value="old('available_hotel_pickup', $route?->available_hotel_pickup ?? false)"
                        hint="Bật nếu tuyến hỗ trợ đón khách tại khách sạn"
                    />

                    <x-admin::form.input
                        name="priority"
                        label="Ưu tiên hiển thị"
                        type="number"
                        :value="old('priority', $route?->priority ?? 0)"
                        hint="Số lớn hơn hiển thị trước"
                    />
                </x-admin::form.section>

                <x-admin::form.section title="Hình ảnh & Nội dung">
                    <x-admin::form.upload
                        name="thumbnail_url"
                        label="Ảnh đại diện"
                        :value="old('thumbnail_url', $route?->thumbnail_url)"
                        hint="Ảnh đại diện cho tuyến đường"
                    />

                    <x-admin::form.upload
                        name="image_list_url[]"
                        label="Album ảnh"
                        :value="old('image_list_url', $route?->image_list_url ?? [])"
                        multiple
                        hint="Nhiều ảnh, kéo để sắp xếp lại"
                    />

                    <x-admin::form.rich-text
                        name="content"
                        label="Nội dung"
                        :value="old('content', $route?->content)"
                        height="350px"
                    />
                </x-admin::form.section>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                <x-admin::form.section>
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md shadow-sm transition-colors">
                        {{ $isEdit ? 'Lưu thay đổi' : 'Tạo tuyến đường' }}
                    </button>
                    @if($isEdit)
                    <a href="{{ route('admin.routes.create') }}" class="block text-center text-xs text-gray-500 hover:text-gray-700 mt-2">+ Thêm tuyến mới</a>
                    @endif
                </x-admin::form.section>

                @if($isEdit)
                <div class="bg-white rounded border border-gray-200 shadow-sm p-4 text-xs text-gray-500 space-y-1">
                    <p>Tạo: {{ $route->created_at?->format('d/m/Y H:i') }}</p>
                    <p>Sửa: {{ $route->updated_at?->format('d/m/Y H:i') }}</p>
                    <p>ID: {{ $route->id }}</p>
                </div>
                @endif
            </div>
        </div>
    </form>

    {{-- Route stops block (edit only) --}}
    @if($isEdit)
        @include('admin.routes._stops-block', ['route' => $route])
    @endif
</div>

<script>
// Auto-generate slug from name on blur
document.addEventListener('DOMContentLoaded', function () {
    var nameInput = document.querySelector('[data-slug-source]');
    var slugInput = document.querySelector('[data-slug-target]');
    if (!nameInput || !slugInput) return;

    nameInput.addEventListener('blur', function () {
        if (slugInput.value.trim() !== '') return; // don't overwrite existing slug
        var slug = nameInput.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd').replace(/Đ/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim().replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        slugInput.value = slug;
    });
});
</script>
@endsection
