@extends('kingexpressbus.admin.layouts.main')
@section('title','Danh sách Tuyến đường')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quản lý Tuyến đường</h3>
        </div>
        <div class="card-body">
            @include('kingexpressbus.admin.partials.alerts')

            <a href="{{ route('admin.routes.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Tạo mới Tuyến đường
            </a>

            <table id="data-table-routes" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Tuyến</th>
                    <th>Điểm đi</th>
                    <th>Điểm đến</th>
                    <th>Khoảng cách (km)</th>
                    <th>Thời gian</th>
                    <th>Giá vé (VNĐ)</th>
                    <th>Ưu tiên</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                @foreach($routes as $route)
                    <tr>
                        <td>{{ $route->id }}</td>
                        <td>{{ $route->title }}</td>
                        <td>{{ $route->province_start_name }}</td>
                        <td>{{ $route->province_end_name }}</td>
                        <td>{{ number_format($route->distance) }}</td>
                        <td>{{ $route->duration }}</td>
                        <td>{{ number_format($route->start_price) }}</td>
                        <td>{{ $route->priority }}</td>
                        <td>
                            <a class="btn btn-warning btn-sm"
                               href="{{ route('admin.routes.edit', ['route' => $route->id]) }}">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('admin.routes.destroy', ['route' => $route->id]) }}"
                                  method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Bạn có chắc muốn xóa Tuyến đường này?')">
                                    <i class="fas fa-trash"></i> Xoá
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            if ($('#data-table-routes').length > 0 && $.fn.DataTable) {
                $('#data-table-routes').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    "language": {"url": "/admin/plugins/datatables/Vietnamese.json"},
                    "order": [[7, "asc"], [0, "desc"]] // Sắp xếp theo Ưu tiên rồi đến ID giảm dần
                }).buttons().container().appendTo('#data-table-routes_wrapper .col-md-6:eq(0)');
            }
        });
    </script>
@endpush
