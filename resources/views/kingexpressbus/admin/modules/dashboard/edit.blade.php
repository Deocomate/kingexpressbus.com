@extends('kingexpressbus.admin.layouts.main') {{-- Kế thừa layout admin --}}
@section('title','Cấu hình Website') {{-- Đặt tiêu đề trang --}}

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Chỉnh sửa Thông tin Website</h3>
        </div>
        {{-- Form gửi đến route admin.dashboard.update --}}
        <form action="{{ route('admin.dashboard.update') }}" method="POST">
            @csrf {{-- Token CSRF --}}
            {{-- Phương thức HTTP là POST như đã định nghĩa trong route --}}

            <div class="card-body">
                {{-- Hiển thị thông báo thành công --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

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

                {{-- === Các trường input cho bảng web_info === --}}

                {{-- Truyền giá trị từ $webInfo vào component --}}
                {{-- Sử dụng old() để giữ lại giá trị khi validation lỗi --}}

                <x-inputs.image-link label="Logo" name="logo" :value="old('logo', $webInfo->logo ?? '')"/>

                <x-inputs.text label="Tiêu đề Website" name="title" :value="old('title', $webInfo->title ?? '')"/>

                <x-inputs.text-area label="Mô tả ngắn (SEO)" name="description"
                                    :value="old('description', $webInfo->description ?? '')"/>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.email label="Email liên hệ" name="email"
                                        :value="old('email', $webInfo->email ?? '')"/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.text label="Số điện thoại" name="phone" :value="old('phone', $webInfo->phone ?? '')"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.text label="Hotline" name="hotline" :value="old('hotline', $webInfo->hotline ?? '')"/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.text label="Chi tiết số điện thoại (Văn bản)" name="phone_detail"
                                       :value="old('phone_detail', $webInfo->phone_detail ?? '')"/>
                    </div>
                </div>


                <x-inputs.text label="Link Website (URL)" name="web_link"
                               :value="old('web_link', $webInfo->web_link ?? '')"/>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.text label="Link Facebook (URL)" name="facebook"
                                       :value="old('facebook', $webInfo->facebook ?? '')"/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.text label="Zalo (Link hoặc SĐT)" name="zalo"
                                       :value="old('zalo', $webInfo->zalo ?? '')"/>
                    </div>
                </div>

                <x-inputs.text label="Địa chỉ" name="address" :value="old('address', $webInfo->address ?? '')"/>

                <x-inputs.text-area label="Mã nhúng Bản đồ (Google Maps iframe)" name="map"
                                    :value="old('map', $webInfo->map ?? '')"/>

                <x-inputs.editor label="Chính sách" name="policy" :value="old('policy', $webInfo->policy ?? '')"/>

                <x-inputs.text-area label="Chi tiết khác" name="detail" :value="old('detail', $webInfo->detail ?? '')"/>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection

{{-- Đảm bảo các scripts cho components (CKEditor, CKFinder) được load trong layout --}}
@push('scripts')
    {{-- Có thể thêm script đặc thù cho trang này nếu cần --}}
@endpush
