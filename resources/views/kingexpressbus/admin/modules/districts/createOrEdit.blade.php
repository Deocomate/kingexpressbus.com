@php
    $isEdit = $district!=null; // Kiểm tra $district có tồn tại và có id không rỗng
    $provinceIdValue = old('province_id', $isEdit ? $district->province_id : $selectedProvinceId);
    // Xử lý images: Đảm bảo là mảng, lấy từ old() trước, sau đó từ $district
    $imagesValue = old('images', ($isEdit && is_array($district->images)) ? $district->images : []);
    // Nếu old('images') không phải là mảng (có thể do lỗi validation trả về chuỗi), thì dùng giá trị từ $district hoặc mảng rỗng
    if (!is_array($imagesValue)) {
         $imagesValue = ($isEdit && is_array($district->images)) ? $district->images : [];
    }

@endphp

@extends('kingexpressbus.admin.layouts.main')
@section('title', $isEdit ? 'Sửa Quận/Huyện' : 'Tạo Quận/Huyện')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            {{-- Sửa lại tiêu đề để xử lý trường hợp $district->name chưa có khi tạo mới --}}
            <h3 class="card-title">{{ $isEdit ? 'Sửa Quận/Huyện: ' . $district?->name : 'Tạo mới Quận/Huyện' }}</h3>
        </div>
        <form
            id="districtForm"
            action="{{ $isEdit ? route('admin.districts.update', ['district' => $district?->id]) : route('admin.districts.store') }}"
            method="post">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="card-body">

                {{-- Thông báo lỗi tổng quát --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Có lỗi xảy ra!</strong> Vui lòng kiểm tra lại các trường dữ liệu.
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Các trường input theo schema và validation --}}

                <x-inputs.select label="Thuộc Tỉnh/Thành phố" name="province_id" required>
                    <option value="">-- Chọn Tỉnh/Thành phố --</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" @selected($provinceIdValue == $province->id)>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </x-inputs.select>

                <x-inputs.text label="Tên Quận/Huyện" name="name" :value="$district?->name" required/>

                <x-inputs.select label="Loại" name="type" required>
                    <option value="">-- Chọn loại --</option>
                    <option value="quan" @selected(old('type', $district?->type) == 'quan')>Quận</option>
                    <option value="huyen" @selected(old('type', $district?->type) == 'huyen')>Huyện</option>
                    <option value="thixa" @selected(old('type', $district?->type) == 'thixa')>Thị xã</option>
                    <option value="thanhpho" @selected(old('type', $district?->type) == 'thanhpho')>Thành phố (thuộc
                        tỉnh)
                    </option>
                </x-inputs.select>

                <x-inputs.text label="Tiêu đề (SEO)" name="title" :value="old('title', $district?->title)" required/>

                <x-inputs.text-area label="Mô tả ngắn (SEO)" name="description"
                                    :value="old('description', $district?->description)" required/>

                <x-inputs.image-link label="Ảnh đại diện" name="thumbnail"
                                     :value="old('thumbnail', $district?->thumbnail)" required/>

                {{-- Chú ý: value cho image-link-array phải là mảng --}}
                <x-inputs.image-link-array label="Thư viện ảnh" name="images" :value="$imagesValue" required/>

                <x-inputs.editor label="Chi tiết" name="detail" :value="old('detail', $district?->detail)" required/>

                <x-inputs.number label="Thứ tự ưu tiên" name="priority" :value="old('priority', $district?->priority)"
                                 required/>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
                <a href="{{ route('admin.districts.index', ['province_id' => $provinceIdValue]) }}"
                   class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
@endpush
