
/* ===== resources/views/kingexpressbus/admin/modules/bus_routes/createOrEdit.blade.php ===== */
@php
    use Carbon\Carbon;
    $isEdit = !empty($busRoute?->id);
@endphp

@extends('kingexpressbus.admin.layouts.main')
@section('title', $isEdit ? 'Sửa Lịch trình xe' : 'Tạo Lịch trình xe')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                {{ $isEdit ? 'Sửa Lịch trình: ' . $busRoute->title : 'Tạo Lịch trình mới' }}
                @if($selectedRoute)
                    <small class="d-block">Cho Tuyến: {{ $selectedRoute->display_name }}</small>
                @endif
            </h3>
        </div>
        <form
            id="busRouteForm"
            action="{{ $isEdit ? route('admin.bus_routes.update', ['bus_route' => $busRoute->id]) : route('admin.bus_routes.store') }}"
            method="post">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            @if(!$isEdit)
                <input type="hidden" name="route_id" value="{{ $selectedRouteId }}">
            @endif

            <div class="card-body">

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

                <x-inputs.select label="Chọn xe cho lịch trình" name="bus_id" required>
                    <option value="">-- Chọn xe --</option>
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}" @selected(old('bus_id', $busRoute?->bus_id) == $bus->id)>
                            {{ $bus->name }}
                        </option>
                    @endforeach
                </x-inputs.select>

                <x-inputs.text label="Tên Lịch trình (Ví dụ: Chuyến sáng, Chuyến tối)" name="title"
                               :value="old('title', $busRoute?->title)" required/>

                <x-inputs.text-area label="Mô tả ngắn về lịch trình" name="description"
                                    :value="old('description', $busRoute?->description)" required/>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.time label="Giờ khởi hành" name="start_at"
                                       :value="old('start_at', $busRoute?->start_at ? Carbon::parse($busRoute->start_at)->format('H:i') : null)"
                                       required/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.time label="Giờ đến (dự kiến)" name="end_at"
                                       :value="old('end_at', $busRoute?->end_at ? Carbon::parse($busRoute->end_at)->format('H:i') : null)"
                                       required/>
                    </div>
                </div>

                <x-inputs.number label="Giá vé (VNĐ)" name="price" type="number" min="0"
                                 :value="old('price', $busRoute?->price ?? 0)" required/>

                <x-inputs.editor label="Chi tiết lịch trình (Điểm đón/trả cụ thể,...)" name="detail"
                                 :value="old('detail', $busRoute?->detail)" required/>

                <x-inputs.number label="Thứ tự ưu tiên" name="priority" type="number"
                                 :value="old('priority', $busRoute?->priority ?? 0)" required/>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
                <a href="{{ route('admin.bus_routes.index', ['route_id' => $selectedRouteId]) }}"
                   class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
@endsection

