@extends('kingexpressbus.admin.layouts.main')
@section('title','Danh sách Loại xe')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quản lý Loại xe</h3>
        </div>
        <div class="card-body">
            @include('kingexpressbus.admin.partials.alerts')

            <a href="{{ route('admin.buses.create') }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Tạo mới Loại xe
            </a>

            <table id="data-table-buses" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Xe</th>
                    <th>Ảnh đại diện</th>
                    <th>Loại xe</th>
                    <th>Số ghế</th>
                    <th>Số tầng</th>
                    <th>Ưu tiên</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                @foreach($buses as $bus)
                    <tr>
                        <td>{{ $bus->id }}</td>
                        <td>{{ $bus->name }}</td>
                        <td>
                            @if($bus->thumbnail)
                                <img src="{{ $bus->thumbnail }}" alt="{{ $bus->name }}"
                                     style="max-width: 100px; max-height: 100px;">
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            {{-- Format lại tên loại xe --}}
                            {{ match ($bus->type) {
                                'sleeper' => 'Giường nằm',
                                'cabin' => 'Cabin đơn',
                                'doublecabin' => 'Cabin đôi',
                                'limousine' => 'Limousine ghế ngồi',
                                default => ucfirst($bus->type)
                            } }}
                        </td>
                        <td>{{ $bus->number_of_seats }}</td>
                        <td>{{ $bus->floors }}</td>
                        <td>{{ $bus->priority }}</td>
                        <td>
                            <a class="btn btn-warning btn-sm"
                               href="{{ route('admin.buses.edit', ['bus' => $bus->id]) }}">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('admin.buses.destroy', ['bus' => $bus->id]) }}"
                                  method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Bạn có chắc muốn xóa Loại xe này?')">
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
            if ($('#data-table-buses').length > 0 && $.fn.DataTable) {
                $('#data-table-buses').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                    "language": {"url": "/admin/plugins/datatables/Vietnamese.json"},
                    "order": [[6, "asc"], [1, "asc"]] // Sắp xếp theo Ưu tiên rồi đến Tên
                }).buttons().container().appendTo('#data-table-buses_wrapper .col-md-6:eq(0)');
            }
        });
    </script>
@endpush
