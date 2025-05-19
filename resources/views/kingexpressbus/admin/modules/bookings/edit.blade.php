@extends('kingexpressbus.admin.layouts.main')
@section('title','Sửa Đặt vé #' . $booking->id)

@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="card card-warning"> {{-- Change card color to warning for editing --}}
        <div class="card-header">
            <h3 class="card-title">Sửa Thông tin Đặt vé #{{ $booking->id }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-eye"></i> Xem chi tiết
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại Danh sách
                </a>
            </div>
        </div>
        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Method Spoofing for UPDATE --}}

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

                {{-- Thông tin cơ bản (không cho sửa) --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Khách hàng:</strong> {{ $booking->customer_fullname }}</p>
                        <p><strong>Ngày đi:</strong> {{ Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                        <p><strong>Ghế:</strong>
                            @if(!empty($booking->seats))
                                @foreach($booking->seats as $seat)
                                    <span class="badge badge-primary mr-1">{{ $seat }}</span>
                                @endforeach
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        {{-- Thêm thông tin chuyến nếu cần --}}
                        <p><strong>Tuyến / Lịch trình:</strong> {{-- Lấy từ join nếu cần --}} </p>
                        <p><strong>Xe:</strong> {{-- Lấy từ join nếu cần --}} </p>
                    </div>
                </div>
                <hr>

                {{-- Trường cho phép sửa --}}
                <div class="row">
                    <div class="col-md-6">
                        {{-- Sử dụng component select đơn giản vì không cần Select2 ở đây --}}
                        <x-inputs.select-simple label="Trạng thái Đặt vé" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status', $booking->status) == $status)>
                                    {{-- Hiển thị tên trạng thái dễ đọc --}}
                                    @switch($status)
                                        @case('pending') Chờ xử lý @break
                                        @case('confirmed') Đã xác nhận @break
                                        @case('cancelled') Đã hủy @break
                                        @case('completed') Đã hoàn thành @break
                                        @default {{ ucfirst($status) }}
                                    @endswitch
                                </option>
                            @endforeach
                        </x-inputs.select-simple>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.select-simple label="Trạng thái Thanh toán" name="payment_status" required>
                            @foreach($paymentStatuses as $pStatus)
                                <option
                                    value="{{ $pStatus }}" @selected(old('payment_status', $booking->payment_status) == $pStatus)>
                                    @switch($pStatus)
                                        @case('paid') Đã thanh toán @break
                                        @case('unpaid') Chưa thanh toán @break
                                        @default {{ ucfirst($pStatus) }}
                                    @endswitch
                                </option>
                            @endforeach
                        </x-inputs.select-simple>
                    </div>
                    {{-- <div class="col-md-4">
                        <x-inputs.select-simple label="Phương thức Thanh toán" name="payment_method" required>
                             @foreach($paymentMethods as $pMethod)
                                <option value="{{ $pMethod }}" @selected(old('payment_method', $booking->payment_method) == $pMethod)>
                                    @switch($pMethod)
                                        @case('online') Trực tuyến @break
                                        @case('offline') Thanh toán sau @break
                                        @default {{ ucfirst($pMethod) }}
                                    @endswitch
                                </option>
                            @endforeach
                        </x-inputs.select-simple>
                    </div> --}}
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- Script cho trang này nếu cần --}}
@endpush
