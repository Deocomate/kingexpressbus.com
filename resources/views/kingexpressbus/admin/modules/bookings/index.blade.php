@extends('kingexpressbus.admin.layouts.main')
@section('title','Danh sách Đặt vé')

@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quản lý Đặt vé</h3>
        </div>
        <div class="card-body">
            @include('kingexpressbus.admin.partials.alerts') {{-- Hiển thị thông báo --}}

            {{-- Optional: Add Filtering Form Here --}}
            {{-- <form method="GET" action="{{ route('admin.bookings.index') }}" class="mb-4">
                <div class="row">
                     <div class="col-md-3">
                        <label>Trạng thái:</label>
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                         <label>Ngày đặt:</label>
                         <input type="date" name="booking_date" class="form-control" value="{{ request('booking_date') }}" onchange="this.form.submit()">
                    </div>
                    Add more filters if needed (customer, route...)
                </div>
            </form> --}}


            <div class="table-responsive">
                <table id="data-table-bookings" class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Tuyến đường</th>
                        <th>Lịch trình / Xe</th>
                        <th>Ngày đi</th>
                        <th>Ghế</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Ngày đặt</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id }}</td>
                            <td>
                                {{ $booking->customer_fullname }}<br>
                                <small>{{ $booking->customer_email }}</small><br>
                                <small>{{ $booking->customer_phone }}</small>
                            </td>
                            <td>{{ $booking->route_title }} ({{ $booking->start_province_name }}
                                -> {{ $booking->end_province_name }})
                            </td>
                            <td>
                                {{ $booking->bus_route_title }}
                                ({{ \Carbon\Carbon::parse($booking->start_at)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($booking->end_at)->format('H:i') }})<br>
                                <small>Xe: {{ $booking->bus_name }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $seats = json_decode($booking->seats, true);
                                    echo is_array($seats) ? implode(', ', $seats) : 'Lỗi';
                                @endphp
                            </td>
                            <td>{!! \App\Http\Controllers\KingExpressBus\Admin\BookingController::formatBookingStatus($booking->status) !!}</td>
                            <td>
                                {!! \App\Http\Controllers\KingExpressBus\Admin\BookingController::formatPaymentStatus($booking->payment_status) !!}
                                <br>
                                <small>({{ \App\Http\Controllers\KingExpressBus\Admin\BookingController::formatPaymentMethod($booking->payment_method) }}
                                    )</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <a class="btn btn-info btn-sm mb-1"
                                   href="{{ route('admin.bookings.show', $booking->id) }}" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm mb-1"
                                   href="{{ route('admin.bookings.edit', $booking->id) }}" title="Sửa trạng thái">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}"
                                      method="POST" style="display: inline-block;"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa đặt vé này? Hành động này không thể hoàn tác.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mb-1" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Không có dữ liệu đặt vé.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Liên kết phân trang --}}
            <div class="mt-3 d-flex justify-content-center">
                {{ $bookings->appends(request()->query())->links() }} {{-- Giữ lại query string khi chuyển trang --}}
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script cho DataTables nếu bạn muốn dùng table có sort/search JS, nếu không phân trang của Laravel là đủ --}}
    {{-- <script>
        $(document).ready(function () {
            // Avoid DataTables if using Laravel pagination primarily
            // $('#data-table-bookings').DataTable({
            //     "responsive": true,
            //     "lengthChange": false,
            //     "autoWidth": false,
            //     "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            //     "language": {"url": "/admin/plugins/datatables/Vietnamese.json"},
            //     "order": [[ 8, "desc" ]], // Order by creation date desc default
            //     "searching": true // Enable search
            // }).buttons().container().appendTo('#data-table-bookings_wrapper .col-md-6:eq(0)');
        });
    </script> --}}
@endpush
