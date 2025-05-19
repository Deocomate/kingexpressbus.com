@extends('kingexpressbus.admin.layouts.main')
@section('title','Danh sách Quận/Huyện')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quản lý Quận/Huyện</h3>
        </div>
        <div class="card-body">
            @include('kingexpressbus.admin.partials.alerts')
            {{-- Form chọn Tỉnh/Thành phố --}}
            <form method="GET" action="{{ route('admin.districts.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="province_id">Chọn Tỉnh/Thành phố:</label>
                            <select name="province_id" id="province_id" class="form-control"
                                    onchange="this.form.submit()">
                                <option value="">-- Tất cả/Chọn Tỉnh/Thành phố --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" @selected($province->id == $selectedProvinceId)>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Chỉ hiển thị nút tạo mới và bảng khi đã chọn tỉnh --}}
            @if($selectedProvinceId)
                @php
                    // Lấy tên tỉnh đang chọn để hiển thị
                    $selectedProvinceName = $provinces->firstWhere('id', $selectedProvinceId)?->name ?? '';
                @endphp

                {{-- Nút tạo mới --}}
                <a href="{{ route('admin.districts.create', ['province_id' => $selectedProvinceId]) }}"
                   class="btn btn-primary mb-3">
                    <i class="fas fa-plus"></i> Tạo mới Quận/Huyện cho {{ $selectedProvinceName }}
                </a>

                {{-- Bảng danh sách --}}
                <table id="data-table-districts" class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Quận/Huyện</th>
                        <th>Loại</th>
                        <th>Ưu tiên</th>
                        <th>Slug</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($districts as $district)
                        <tr>
                            <td>{{ $district->id }}</td>
                            <td>{{ $district->name }}</td>
                            <td>
                                {{-- Hiển thị tên loại dễ đọc hơn --}}
                                @switch($district->type)
                                    @case('quan') Quận @break
                                    @case('huyen') Huyện @break
                                    @case('thixa') Thị xã @break
                                    @case('thanhpho') Thành phố (thuộc tỉnh) @break
                                    @default {{ $district->type }}
                                @endswitch
                            </td>
                            <td>{{ $district->priority ?? 0 }}</td>
                            <td>{{ $district->slug }}</td>
                            <td>
                                {{-- Nút Sửa --}}
                                <a class="btn btn-warning btn-sm"
                                   href="{{ route('admin.districts.edit', ['district' => $district->id]) }}">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                {{-- Nút Xóa --}}
                                <form action="{{ route('admin.districts.destroy', ['district' => $district->id]) }}"
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Bạn có chắc muốn xóa Quận/Huyện này?')">
                                        <i class="fas fa-trash"></i> Xoá
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-info">Vui lòng chọn một Tỉnh/Thành phố để xem danh sách Quận/Huyện.</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script cho DataTables nếu sử dụng --}}
    <script>
        $(document).ready(function () {
            // Chỉ khởi tạo DataTable khi có bảng dữ liệu được hiển thị
            if ($('#data-table-districts').length > 0 && $.fn.DataTable) {
                $('#data-table-districts').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    "language": {"url": "/admin/plugins/datatables/Vietnamese.json"},
                    "order": [[3, "asc"], [1, "asc"]] // Sắp xếp theo Ưu tiên rồi đến Tên
                }).buttons().container().appendTo('#data-table-districts_wrapper .col-md-6:eq(0)');
            }
        });
    </script>
@endpush
