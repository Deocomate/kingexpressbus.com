<?php
/**
 * @var \Illuminate\Support\Collection|\stdClass[] $provinces // Sửa kiểu dữ liệu nếu cần
 */
?>
@extends('kingexpressbus.admin.layouts.main') {{-- Giả sử layout admin là admin.layouts.main [cite: 114] --}}
@section('title','Danh sách Tỉnh/Thành phố') {{-- [cite: 114] --}}
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Tỉnh/Thành phố</h3>
        </div>
        <div class="card-body">
            {{-- Hiển thị thông báo thành công/lỗi --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Nút tạo mới [cite: 114] --}}
            <a href="{{ route('admin.provinces.create') }}" class="btn btn-primary mb-3">Tạo mới Tỉnh/Thành phố</a>

            <table id="data-table-provinces" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    {{-- Các cột dựa trên bảng provinces [cite: 115, 116, 265, 266] --}}
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Loại</th>
                    <th>Ảnh đại diện</th>
                    <th>Ưu tiên</th>
                    <th>Slug</th>
                    <th>Hành động</th> {{-- [cite: 115] --}}
                </tr>
                </thead>
                <tbody>
                @foreach($provinces as $province)
                    {{-- [cite: 116] --}}
                    <tr>
                        <td>{{ $province->id }}</td>
                        <td>{{ $province->name }}</td> {{-- [cite: 117] --}}
                        <td>
                            {{-- Hiển thị tên loại dễ đọc hơn --}}
                            @if($province->type == 'thanhpho')
                                Thành phố
                            @elseif($province->type == 'tinh')
                                Tỉnh
                            @else
                                {{ $province->type }}
                            @endif
                        </td>
                        <td>
                            {{-- Hiển thị ảnh thumbnail [cite: 118] --}}
                            @if($province->thumbnail)
                                <img src="{{ $province->thumbnail }}" alt="{{ $province->name }}"
                                     style="max-width: 100px; max-height: 100px;"> {{-- [cite: 119] --}}
                            @else
                                Không có
                            @endif
                        </td>
                        <td>{{ $province->priority ?? 0 }}</td> {{-- [cite: 122] --}}
                        <td>{{ $province->slug }}</td>
                        <td>
                            {{-- Nút Sửa [cite: 123] --}}
                            <a class="btn btn-warning btn-sm"
                               href="{{ route('admin.provinces.edit', ['province' => $province->id]) }}">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            {{-- Nút Xóa [cite: 123] --}}
                            <form action="{{ route('admin.provinces.destroy', ['province' => $province->id]) }}"
                                  method="POST" style="display: inline-block;">
                                @csrf {{-- [cite: 124] --}}
                                @method('DELETE') {{-- [cite: 124] --}}
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Bạn có chắc muốn xóa Tỉnh/Thành phố này?')"> {{-- [cite: 125] --}}
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
    {{-- Script cho DataTables nếu sử dụng [cite: 127, 128] --}}
    <script>
        // Apply data table
        $(document).ready(function () {
            $('#data-table-provinces').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "language": {"url": "/admin/plugins/datatables/Vietnamese.json"},
                "order": [[4, "asc"], [1, "asc"]] // Sắp xếp mặc định theo cột Ưu tiên rồi đến Tên
            }).buttons().container().appendTo('#data-table-provinces_wrapper .col-md-6:eq(0)');
        });
    </script>
@endpush
