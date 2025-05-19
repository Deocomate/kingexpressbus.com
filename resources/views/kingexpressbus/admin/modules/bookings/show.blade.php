@extends('kingexpressbus.admin.layouts.main')
@section('title','Chi tiết Đặt vé #' . $booking->id)

@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Chi tiết Đặt vé #{{ $booking->id }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại Danh sách
                </a>
                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Sửa trạng thái
                </a>
                <form action="{{ route('admin.bookings.destroy', $booking->id) }}"
                      method="POST" style="display: inline-block;"
                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa đặt vé này? Hành động này không thể hoàn tác.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @include('kingexpressbus.admin.partials.alerts') {{-- Hiển thị thông báo --}}

            <div class="row">
                {{-- Thông tin Khách hàng --}}
                <div class="col-md-6 mb-3">
                    <h5>Thông tin Khách hàng</h5>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th style="width: 150px;">Họ tên</th>
                            <td>{{ $booking->customer_fullname }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $booking->customer_email }}</td>
                        </tr>
                        <tr>
                            <th>Điện thoại</th>
                            <td>{{ $booking->customer_phone }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ</th>
                            <td>{{ $booking->customer_address ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Thông tin Đặt vé --}}
                <div class="col-md-6 mb-3">
                    <h5>Thông tin Đặt vé</h5>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th style="width: 150px;">Mã đặt vé</th>
                            <td>#{{ $booking->id }}</td>
                        </tr>
                        <tr>
                            <th>Ngày đi</th>
                            <td>{{ Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Ghế đã đặt</th>
                            <td>
                                @if(!empty($booking->seats))
                                    @foreach($booking->seats as $seat)
                                        <span class="badge badge-primary mr-1">{{ $seat }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>{!! \App\Http\Controllers\KingExpressBus\Admin\BookingController::formatBookingStatus($booking->status) !!}</td>
                        </tr>
                        <tr>
                            <th>Thanh toán</th>
                            <td>{!! \App\Http\Controllers\KingExpressBus\Admin\BookingController::formatPaymentStatus($booking->payment_status) !!}
                                ({{ \App\Http\Controllers\KingExpressBus\Admin\BookingController::formatPaymentMethod($booking->payment_method) }}
                                )
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày đặt</th>
                            <td>{{ Carbon::parse($booking->created_at)->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                {{-- Thông tin Chuyến đi --}}
                <div class="col-12">
                    <h5>Thông tin Chuyến đi</h5>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th style="width: 150px;">Tuyến đường</th>
                            <td>{{ $booking->route_title }} ({{ $booking->start_province_name }}
                                -> {{ $booking->end_province_name }})
                            </td>
                        </tr>
                        <tr>
                            <th>Lịch trình</th>
                            <td>{{ $booking->bus_route_title }} ({{ Carbon::parse($booking->start_at)->format('H:i') }}
                                - {{ Carbon::parse($booking->end_at)->format('H:i') }})
                            </td>
                        </tr>
                        <tr>
                            <th>Xe</th>
                            <td>{{ $booking->bus_name }} ({{ $booking->bus_type }})</td>
                        </tr>
                        <tr>
                            <th>Khoảng cách</th>
                            <td>{{ $booking->distance ? number_format($booking->distance) . ' km' : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Thời gian dự kiến</th>
                            <td>{{ $booking->duration ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
