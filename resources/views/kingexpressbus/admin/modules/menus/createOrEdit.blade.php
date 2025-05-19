@php
    // Xác định xem đây là form sửa hay tạo mới
    $isEdit = isset($menu) && $menu->id;
    // Giá trị parent_id, ưu tiên old() nếu có lỗi validation
    $parentIdValue = old('parent_id', $isEdit ? $menu->parent_id : null);
@endphp

@extends('kingexpressbus.admin.layouts.main') {{-- Kế thừa layout admin --}}
@section('title', $isEdit ? 'Sửa Menu' : 'Tạo Menu Mới') {{-- Tiêu đề trang --}}

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $isEdit ? 'Sửa Menu: ' . $menu->name : 'Tạo Menu Mới' }}</h3>
        </div>
        {{-- Form action trỏ đến store hoặc update route --}}
        <form action="{{ $isEdit ? route('admin.menus.update', ['menu' => $menu->id]) : route('admin.menus.store') }}"
              method="POST">
            @csrf
            @if($isEdit)
                @method('PUT') {{-- Method Spoofing cho update --}}
            @endif

            <div class="card-body">
                {{-- Hiển thị lỗi validation nếu có --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Có lỗi xảy ra!</strong> Vui lòng kiểm tra lại các trường dữ liệu.
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Sử dụng Blade components cho input --}}
                <x-inputs.text label="Tên Menu" name="name" :value="old('name', $menu->name ?? '')" required/>

                <x-inputs.text label="Đường dẫn (URL)" name="url" :value="old('url', $menu->url ?? '')"/>

                <x-inputs.number label="Thứ tự ưu tiên" name="priority" type="number"
                                 :value="old('priority', $menu->priority ?? 0)" required/>

                {{-- Dropdown chọn Menu cha --}}
                <x-inputs.select label="Menu Cha" name="parent_id">
                    <option value="-1">-- Không có (Là menu gốc) --</option>
                    {{-- $formattedParentMenus được truyền từ Controller --}}
                    @foreach($formattedParentMenus ?? [] as $parentMenu)
                        <option value="{{ $parentMenu->id }}" @selected($parentIdValue == $parentMenu->id)>
                            {{ $parentMenu->name }}
                        </option>
                    @endforeach
                </x-inputs.select>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Script cho Select2 nếu component <x-inputs.select> sử dụng nó --}}
@endpush
