<?php
// Xác định xem đây là form sửa hay tạo mới [cite: 129]
$isEdit = isset($province) && $province;
// Đảm bảo $province->images là mảng (đã được xử lý trong controller)
$imagesValue = old('images', ($isEdit && is_array($province->images)) ? $province->images : []);
?>
@extends('kingexpressbus.admin.layouts.main') {{-- [cite: 129] --}}
@section('title', $isEdit ? 'Sửa Tỉnh/Thành phố' : 'Tạo Tỉnh/Thành phố') {{-- [cite: 129] --}}
@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $isEdit ? 'Sửa Tỉnh/Thành phố: ' . $province->name : 'Tạo mới Tỉnh/Thành phố' }}</h3> {{-- [cite: 130] --}}
        </div>
        <form
            id="provinceForm"
            action="{{ $isEdit ? route('admin.provinces.update', ['province' => $province->id]) : route('admin.provinces.store') }}"
            {{-- [cite: 130] --}}
            method="post">
            @csrf {{-- [cite: 130] --}}
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="card-body">

                <x-inputs.text label="Tên Tỉnh/Thành phố" name="name" :value="old('name', $province?->name)"/>

                <x-inputs.select label="Loại" name="type" required>
                    <option value="thanhpho" @selected(old('type', $province?->type) == 'thanhpho')>Thành phố</option>
                    <option value="tinh" @selected(old('type', $province?->type) == 'tinh')>Tỉnh</option>
                </x-inputs.select>

                <x-inputs.text label="Tiêu đề (SEO)" name="title" :value="old('title', $province?->title)"/>

                <x-inputs.text-area label="Mô tả ngắn (SEO)" name="description"
                                    :value="old('description', $province?->description)"/>

                <x-inputs.image-link label="Ảnh đại diện" name="thumbnail"
                                     :value="old('thumbnail', $province?->thumbnail)"/>

                <x-inputs.image-link-array label="Thư viện ảnh" name="images" :value="$imagesValue"/>

                <x-inputs.editor label="Chi tiết" name="detail"
                                 :value="old('detail', $province?->detail)"/>

                <x-inputs.number label="Thứ tự ưu tiên" name="priority"
                                 :value="old('priority', $province?->priority ?? 0)"/>

            </div>
            <div class="card-footer">
                <button type="submit"
                        class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button> {{-- [cite: 136] --}}
                <a href="{{ route('admin.provinces.index') }}" class="btn btn-secondary">Hủy</a> {{-- [cite: 136] --}}
            </div>
        </form>
    </div>
@endsection

{{-- Đẩy script cần thiết cho các component (ví dụ: CKEditor, Select2, CKFinder) nếu chưa có trong layout chính [cite: 44, 54, 61] --}}
@push('scripts')
@endpush
