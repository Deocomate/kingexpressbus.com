<x-admin.layout title="Sửa Quy tắc Phụ thu">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.holiday-surcharges.index') }}">Phụ thu Lễ/Tết</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </x-slot:breadcrumb>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Cập nhật quy tắc: {{ $rule->name }}</h5>
        </div>
        <form action="{{ route('admin.holiday-surcharges.update', $rule->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        Vui lòng kiểm tra lại dữ liệu nhập.
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Tên quy tắc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $rule->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Từ ngày <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ old('start_date', \Carbon\Carbon::parse($rule->start_date)->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Đến ngày <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ old('end_date', \Carbon\Carbon::parse($rule->end_date)->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="global_surcharge_amount">Phụ thu Global (VND) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="global_surcharge_amount" name="global_surcharge_amount"
                                value="{{ old('global_surcharge_amount', $rule->global_surcharge_amount) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="priority">Ưu tiên</label>
                            <input type="number" class="form-control" id="priority" name="priority"
                                value="{{ old('priority', $rule->priority) }}" min="0">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $rule->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Đang áp dụng</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason">Lý do tăng giá</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3">{{ old('reason', $rule->reason) }}</textarea>
                </div>

                <hr>
                <h6 class="mb-3">Phụ thu riêng theo tuyến (để 0 nếu không áp dụng)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Tuyến đường</th>
                                <th style="width: 220px;">Phụ thu thêm (VND)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($routes as $route)
                                @php
                                    $defaultValue = $adjustmentMap[$route->id] ?? 0;
                                @endphp
                                <tr>
                                    <td>{{ $route->name }} ({{ $route->start_province_name }} → {{ $route->end_province_name }})</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm"
                                            name="route_adjustments[{{ $route->id }}][route_surcharge_amount]"
                                            value="{{ old('route_adjustments.' . $route->id . '.route_surcharge_amount', $defaultValue) }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.holiday-surcharges.index') }}" class="btn btn-secondary">Quay lại</a>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</x-admin.layout>
