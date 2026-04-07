<x-admin.layout title="Quản lý Phụ thu Lễ/Tết">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Phụ thu Lễ/Tết</li>
    </x-slot:breadcrumb>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Quy tắc phụ thu ngày lễ/tết</h4>
        <a href="{{ route('admin.holiday-surcharges.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm quy tắc mới
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên quy tắc</th>
                            <th>Khoảng ngày áp dụng</th>
                            <th>Phụ thu Global</th>
                            <th>Số tuyến điều chỉnh riêng</th>
                            <th>Trạng thái</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rules as $rule)
                            <tr>
                                <td>{{ $rule->id }}</td>
                                <td>
                                    <strong>{{ $rule->name }}</strong>
                                    @if (!empty($rule->reason))
                                        <p class="text-muted mb-0 mt-1" style="font-size: 12px;">{{ $rule->reason }}</p>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($rule->start_date)->format('d/m/Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($rule->end_date)->format('d/m/Y') }}
                                </td>
                                <td>{{ number_format($rule->global_surcharge_amount) }}đ</td>
                                <td>{{ $rule->route_adjustments_count }}</td>
                                <td>
                                    @if ($rule->is_active)
                                        <span class="badge badge-success">Đang áp dụng</span>
                                    @else
                                        <span class="badge badge-secondary">Tắt</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.holiday-surcharges.edit', $rule->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.holiday-surcharges.destroy', $rule->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa quy tắc này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Chưa có quy tắc phụ thu nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin.layout>
