@php
    $isEdit = !empty($bus?->id);
    // Decode JSON images + services, đảm bảo là mảng
    $imagesValue = old('images', ($isEdit && is_array($bus->images)) ? $bus->images : []);
    if (!is_array($imagesValue)) { $imagesValue = []; }

    $servicesValue = old('services', ($isEdit && is_array($bus->services)) ? $bus->services : []);
     if (!is_array($servicesValue)) { $servicesValue = []; }
@endphp

@extends('kingexpressbus.admin.layouts.main')
@section('title', $isEdit ? 'Sửa Loại xe' : 'Tạo Loại xe')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $isEdit ? 'Sửa Loại xe: ' . $bus->name : 'Tạo mới Loại xe' }}</h3>
        </div>
        <form
            id="busForm"
            action="{{ $isEdit ? route('admin.buses.update', ['bus' => $bus->id]) : route('admin.buses.store') }}"
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

                {{-- Input fields theo schema và validation (required) --}}

                <x-inputs.text label="Tiêu đề (Hiển thị chính)" name="title" :value="old('title', $bus?->title)"
                               required/>

                <x-inputs.text-area label="Mô tả ngắn" name="description"
                                    :value="old('description', $bus?->description)" required/>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.text label="Tên xe (Ví dụ: Limousine A)" name="name" :value="old('name', $bus?->name)"
                                       required/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.text label="Tên dòng xe/Model (Ví dụ: Hyundai Universe)" name="model_name"
                                       :value="old('model_name', $bus?->model_name)" required/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-inputs.select label="Loại xe" name="type" required>
                            <option value="">-- Chọn loại xe --</option>
                            <option value="sleeper" @selected(old('type', $bus?->type) == 'sleeper')>Giường nằm</option>
                            <option value="cabin" @selected(old('type', $bus?->type) == 'cabin')>Cabin đơn</option>
                            <option value="doublecabin" @selected(old('type', $bus?->type) == 'doublecabin')>Cabin đôi
                            </option>
                            <option value="limousine" @selected(old('type', $bus?->type) == 'limousine')>Limousine ghế
                                ngồi
                            </option>
                        </x-inputs.select>
                    </div>
                    <div class="col-md-4">
                        <x-inputs.number label="Số ghế/giường" name="number_of_seats" type="number" min="1"
                                         :value="old('number_of_seats', $bus?->number_of_seats)" required/>
                    </div>
                    <div class="col-md-4">
                        <x-inputs.number label="Số tầng" name="floors" type="number" min="1"
                                         :value="old('floors', $bus?->floors)" required/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.number label="Số hàng ghế/giường" name="seat_row_number" type="number" min="1"
                                         :value="old('seat_row_number', $bus?->seat_row_number)" required/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.number label="Số cột ghế/giường" name="seat_column_number" type="number" min="1"
                                         :value="old('seat_column_number', $bus?->seat_column_number)" required/>
                    </div>
                </div>

                <x-inputs.image-link label="Ảnh đại diện" name="thumbnail" :value="old('thumbnail', $bus?->thumbnail)"
                                     required/>

                <x-inputs.image-link-array label="Thư viện ảnh xe" name="images" :value="$imagesValue" required/>

                {{-- Dùng text-array cho services --}}
                <x-inputs.text-array label="Dịch vụ/Tiện ích trên xe" name="services" :value="$servicesValue" required/>

                <x-inputs.editor label="Chi tiết về loại xe" name="detail" :value="old('detail', $bus?->detail)"
                                 required/>

                <x-inputs.number label="Thứ tự ưu tiên hiển thị" name="priority" type="number"
                                 :value="old('priority', $bus?->priority)" required/>

                {{-- Không có input cho slug --}}

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
                <a href="{{ route('admin.buses.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Scripts for editor, image uploader, text-array etc. --}}
@endpush
