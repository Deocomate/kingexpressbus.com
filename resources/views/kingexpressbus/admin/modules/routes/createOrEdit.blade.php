@php
    $isEdit = !empty($route?->id);
    // Xử lý images: Đảm bảo là mảng, lấy từ old() trước, sau đó từ $route
    $imagesValue = old('images', ($isEdit && is_array($route->images)) ? $route->images : []);
    if (!is_array($imagesValue)) {
         $imagesValue = ($isEdit && is_array($route->images)) ? $route->images : [];
    }
@endphp

@extends('kingexpressbus.admin.layouts.main')
@section('title', $isEdit ? 'Sửa Tuyến đường' : 'Tạo Tuyến đường')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $isEdit ? 'Sửa Tuyến đường: ' . $route->title : 'Tạo mới Tuyến đường' }}</h3>
        </div>
        <form
            id="routeForm"
            action="{{ $isEdit ? route('admin.routes.update', ['route' => $route->id]) : route('admin.routes.store') }}"
            method="post">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="card-body">

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

                {{-- Input fields --}}
                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.select label="Điểm đi" name="province_id_start" required>
                            <option value="">-- Chọn điểm đi --</option>
                            @foreach($provinces as $province)
                                <option
                                    value="{{ $province->id }}" @selected(old('province_id_start', $route?->province_id_start) == $province->id)>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </x-inputs.select>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.select label="Điểm đến" name="province_id_end" required>
                            <option value="">-- Chọn điểm đến --</option>
                            @foreach($provinces as $province)
                                <option
                                    value="{{ $province->id }}" @selected(old('province_id_end', $route?->province_id_end) == $province->id)>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </x-inputs.select>
                    </div>
                </div>

                <x-inputs.text label="Tên Tuyến đường (Tiêu đề)" name="title" :value="old('title', $route?->title)"
                               required/>

                <x-inputs.text-area label="Mô tả ngắn" name="description"
                                    :value="old('description', $route?->description)" required/>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.number label="Khoảng cách (km)" name="distance" type="number" min="0"
                                         :value="old('distance', $route?->distance)" required/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.text label="Thời gian di chuyển (Ví dụ: 3 giờ 30 phút)" name="duration"
                                       :value="old('duration', $route?->duration)" required/>
                    </div>
                </div>

                <x-inputs.number label="Giá vé khởi điểm (VNĐ)" name="start_price" type="number" min="0"
                                 :value="old('start_price', $route?->start_price)" required/>

                <x-inputs.image-link label="Ảnh đại diện" name="thumbnail" :value="old('thumbnail', $route?->thumbnail)"
                                     required/>

                <x-inputs.image-link-array label="Thư viện ảnh" name="images" :value="$imagesValue" required/>

                <x-inputs.editor label="Chi tiết tuyến đường" name="detail" :value="old('detail', $route?->detail)"
                                 required/>

                <x-inputs.number label="Thứ tự ưu tiên" name="priority" type="number"
                                 :value="old('priority', $route?->priority)" required/>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
                <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Scripts for editor, select2 (if needed) --}}
@endpush
