@php
    use Carbon\Carbon;
    $isEdit = !empty($route?->id);
    // Xử lý images: Đảm bảo là mảng, lấy từ old() trước, sau đó từ $route
    $imagesValue = old('images', ($isEdit && is_array($route->images)) ? $route->images : []);
    if (!is_array($imagesValue)) {
         $imagesValue = ($isEdit && is_array($route->images)) ? $route->images : [];
    }
    // Xử lý stops
    $stopsToDisplay = old('stops', $existingStops ?? []);
    if (!is_array($stopsToDisplay) && !$stopsToDisplay instanceof \Illuminate\Support\Collection) {
        $stopsToDisplay = [];
    }
@endphp

@extends('kingexpressbus.admin.layouts.main')
@section('title', $isEdit ? 'Sửa Tuyến đường' : 'Tạo Tuyến đường')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $isEdit ? 'Sửa Tuyến đường: ' . $route->title : 'Tạo mới Tuyến đường' }}</h3>
        </div>
        <form
            id="routeForm"
            action="{{ $isEdit ? route('admin.routes.update', ['route' => $route->id]) : route('admin.routes.store') }}"
            method="post">
            @csrf
            @if($isEdit)
                @method('PUT')
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
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                @endif

                {{-- Input fields --}}
                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.select label="Điểm đi" name="province_id_start" required>
                            <option value="">-- Chọn điểm đi --</option>
                            @foreach($provinces as $province)
                                <option
                                    value="{{ $province->id }}" @selected(old('province_id_start', $route?->province_id_start) == $province->id)>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </x-inputs.select>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.select label="Điểm đến" name="province_id_end" required>
                            <option value="">-- Chọn điểm đến --</option>
                            @foreach($provinces as $province)
                                <option
                                    value="{{ $province->id }}" @selected(old('province_id_end', $route?->province_id_end) == $province->id)>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </x-inputs.select>
                    </div>
                </div>

                <x-inputs.text label="Tên Tuyến đường (Tiêu đề)" name="title" :value="old('title', $route?->title)"
                               required/>

                <x-inputs.text-area label="Mô tả ngắn" name="description"
                                    :value="old('description', $route?->description)" required/>

                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.number label="Khoảng cách (km)" name="distance" type="number" min="0"
                                         :value="old('distance', $route?->distance)" required/>
                    </div>
                    <div class="col-md-6">
                        <x-inputs.text label="Thời gian di chuyển (Ví dụ: 3 giờ 30 phút)" name="duration"
                                       :value="old('duration', $route?->duration)" required/>
                    </div>
                </div>

                <x-inputs.number label="Giá vé khởi điểm (VNĐ)" name="start_price" type="number" min="0"
                                 :value="old('start_price', $route?->start_price)" required/>


                <x-inputs.image-link label="Ảnh đại diện" name="thumbnail" :value="old('thumbnail', $route?->thumbnail)"
                                     required/>

                <x-inputs.image-link-array label="Thư viện ảnh" name="images" :value="$imagesValue" required/>

                <x-inputs.editor label="Chi tiết tuyến đường" name="detail" :value="old('detail', $route?->detail)"
                                 required/>

                <x-inputs.number label="Thứ tự ưu tiên" name="priority" type="number"
                                 :value="old('priority', $route?->priority ?? 0)" required/>

                {{-- === Stops Management Section === --}}
                <div class="card card-secondary mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Quản lý Điểm đón/trả cho Tuyến đường</h3>
                    </div>
                    <div class="card-body">
                        <div id="stops-container">
                            @foreach ($stopsToDisplay as $index => $stop)
                                @php
                                    $districtId = is_array($stop) ? ($stop['district_id'] ?? null) : ($stop->district_id ?? null);
                                    $stopAt = is_array($stop) ? ($stop['stop_at'] ?? null) : ($stop->stop_at ?? null);
                                    $titleValue = is_array($stop) ? ($stop['title'] ?? null) : ($stop->title ?? null);
                                    $stopAtFormatted = $stopAt ? Carbon::parse($stopAt)->format('H:i') : '';
                                @endphp
                                <div class="stop-item row mb-3 align-items-center border-bottom pb-3">
                                    <div class="col-md-5">
                                        <div class="form-group mb-0">
                                            <label class="mb-1">Quận/Huyện/Địa điểm <span
                                                    class="text-danger">*</span></label>
                                            <select name="stops[{{ $index }}][district_id]"
                                                    class="form-control select2-district" style="width: 100%;" required>
                                                <option value="">-- Chọn địa điểm --</option>
                                                @foreach($districtsByProvince ?? [] as $provinceName => $districts)
                                                    <optgroup label="{{ $provinceName }}">
                                                        @foreach($districts as $district)
                                                            <option
                                                                value="{{ $district->id }}" @selected($districtId == $district->id)>
                                                                {{ $district->name }} ({{ $district->type }})
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="mb-1">Tên điểm dừng (Nếu có)</label>
                                            <input type="text" name="stops[{{ $index }}][title]" class="form-control"
                                                   placeholder="Ví dụ: Bến xe Giáp Bát" value="{{ $titleValue }}">
                                        </div>
                                    </div>
                                    <div class="col-md-1 pt-3">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-stop w-100 mt-1">
                                            Xoá
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="btn-add-stop" class="btn btn-secondary mt-2"><i
                                class="fas fa-plus"></i> Thêm Điểm dừng
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
                <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>

    {{-- Hidden template for adding new stops --}}
    <div id="stop-template" style="display: none;">
        <div class="stop-item row mb-3 align-items-center border-bottom pb-3">
            <div class="col-md-5">
                <div class="form-group mb-0">
                    <label class="mb-1">Quận/Huyện/Địa điểm <span class="text-danger">*</span></label>
                    <select name="stops[__INDEX__][district_id]" class="form-control select2-district-template"
                            style="width: 100%;" required>
                        <option value="">-- Chọn địa điểm --</option>
                        @foreach($districtsByProvince ?? [] as $provinceName => $districts)
                            <optgroup label="{{ $provinceName }}">
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">
                                        {{ $district->name }} ({{ $district->type }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="mb-1">Tên điểm dừng (Nếu có)</label>
                    <input type="text" name="stops[__INDEX__][title]" class="form-control"
                           placeholder="Ví dụ: Ngã tư Sở">
                </div>
            </div>
            <div class="col-md-1 pt-3">
                <button type="button" class="btn btn-danger btn-sm btn-remove-stop w-100 mt-1">Xoá</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let stopIndex = {{ count($stopsToDisplay) }};
            const stopsContainer = $('#stops-container');
            const stopTemplateHtml = $('#stop-template').html();

            function initializeSelect2(element) {
                $(element).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: '-- Chọn địa điểm --'
                });
            }

            $('.select2-district').each(function () {
                initializeSelect2(this);
            });

            $('#btn-add-stop').on('click', function () {
                let newStopHtml = stopTemplateHtml.replace(/__INDEX__/g, stopIndex);
                let $newStopElement = $(newStopHtml);

                stopsContainer.append($newStopElement);

                let newSelect = $newStopElement.find('.select2-district-template');
                initializeSelect2(newSelect);
                newSelect.removeClass('select2-district-template').addClass('select2-district');

                stopIndex++;
            });

            stopsContainer.on('click', '.btn-remove-stop', function () {
                $(this).closest('.stop-item').remove();
            });
        });
    </script>
@endpush
