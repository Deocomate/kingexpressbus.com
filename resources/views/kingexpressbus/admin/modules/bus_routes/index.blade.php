{{-- resources\views\kingexpressbus\admin\modules\bus_routes\index.blade.php --}}
@extends('kingexpressbus.admin.layouts.main')
@section('title','Danh sách Lịch trình xe')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quản lý Lịch trình xe</h3>
        </div>
        <div class="card-body">
            @include('kingexpressbus.admin.partials.alerts')

            {{-- Form chọn Tuyến đường --}}
            <form method="GET" action="{{ route('admin.bus_routes.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-6"> {{-- Tăng chiều rộng dropdown --}}
                        <div class="form-group">
                            <label for="route_id">Chọn Tuyến đường:</label>
                            <select name="route_id" id="route_id" class="form-control select2"
                                    {{-- Thêm class select2 nếu dùng --}} onchange="this.form.submit()">
                                <option value="">-- Chọn Tuyến đường --</option>
                                @foreach($routes as $route)
                                    {{-- Sử dụng display_name đã tạo trong controller --}}
                                    <option value="{{ $route->id }}" @selected($route->id == $selectedRouteId)>
                                        {{ $route->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            @if($selectedRouteId)
                @php
                    // Lấy tên tuyến đường đang chọn để hiển thị
                    $selectedRouteDisplayName = $routes->firstWhere('id', $selectedRouteId)?->display_name ?? '';
                @endphp

                {{-- Nút tạo mới --}}
                <a href="{{ route('admin.bus_routes.create', ['route_id' => $selectedRouteId]) }}"
                   class="btn btn-primary mb-3">
                    <i class="fas fa-plus"></i> Thêm Lịch trình cho
                    Tuyến: {{ Str::limit($selectedRouteDisplayName, 50) }} {{-- Giới hạn độ dài tên tuyến --}}
                </a>

                {{-- Bảng danh sách --}}
                <table id="data-table-bus-routes" class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Lịch trình</th>
                        <th>Tên Xe</th>
                        <th>Giờ đi</th>
                        <th>Giờ đến</th>
                        <th>Giá vé (VNĐ)</th> {{-- Thêm cột Giá vé --}}
                        <th>Ưu tiên</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($busRoutes as $busRoute)
                        <tr>
                            <td>{{ $busRoute->id }}</td>
                            <td>{{ $busRoute->title }}</td>
                            <td>{{ $busRoute->bus_name }}</td> {{-- Lấy từ join --}}
                            <td>{{ \Carbon\Carbon::parse($busRoute->start_at)->format('H:i') }}</td> {{-- Format giờ --}}
                            <td>{{ \Carbon\Carbon::parse($busRoute->end_at)->format('H:i') }}</td> {{-- Format giờ --}}
                            <td>{{ number_format($busRoute->price) }}</td> {{-- Hiển thị giá vé --}}
                            <td>{{ $busRoute->priority }}</td>
                            <td>
                                <a class="btn btn-warning btn-sm"
                                   href="{{ route('admin.bus_routes.edit', ['bus_route' => $busRoute->id]) }}">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <form action="{{ route('admin.bus_routes.destroy', ['bus_route' => $busRoute->id]) }}"
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Bạn có chắc muốn xóa Lịch trình này?')">
                                        <i class="fas fa-trash"></i> Xoá
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-info">Vui lòng chọn một Tuyến đường để xem danh sách Lịch trình xe.</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            if ($('#data-table-bus-routes').length > 0 && $.fn.DataTable) {
                $('#data-table-bus-routes').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    "language": {"url": "/admin/plugins/datatables/Vietnamese.json"},
                    "order": [[6, "asc"], [3, "asc"]] // Cập nhật index cột order (Ưu tiên rồi đến Giờ đi)
                }).buttons().container().appendTo('#data-table-bus-routes_wrapper .col-md-6:eq(0)');
            }
        });
    </script>
@endpush
