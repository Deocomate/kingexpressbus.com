@php
    use Carbon\Carbon;
        $isEdit = !empty($busRoute?->id);
        // Combine old input with existing data for repopulation of stops
        // Ensure $existingStops is available (it's passed from controller)
        $stopsToDisplay = old('stops', $existingStops ?? []);
        if (!is_array($stopsToDisplay) && !$stopsToDisplay instanceof \Illuminate\Support\Collection) {
            $stopsToDisplay = [];
        }
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

            {{-- Hidden input for route_id when creating --}}
            @if(!$isEdit)
                <input type="hidden" name="route_id" value="{{ $selectedRouteId }}">
            @endif

            <div class="card-body">

                {{-- General Error Display --}}
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

                {{-- === Bus Route Fields === --}}
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

                <x-inputs.editor label="Chi tiết lịch trình (Điểm đón/trả cụ thể,...)" name="detail"
                                 :value="old('detail', $busRoute?->detail)" required/>

                <x-inputs.number label="Thứ tự ưu tiên" name="priority" type="number"
                                 :value="old('priority', $busRoute?->priority ?? 0)" required/>


                {{-- === Stops Management Section === --}}
                <div class="card card-secondary mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Quản lý Điểm đón/trả</h3>
                    </div>
                    <div class="card-body">
                        <div id="stops-container">
                            {{-- Display existing/old stops --}}
                            @foreach ($stopsToDisplay as $index => $stop)
                                @php
                                    // Handle both array (from old input) and object (from DB)
                                    $districtId = is_array($stop) ? ($stop['district_id'] ?? null) : ($stop->district_id ?? null);
                                    $stopAt = is_array($stop) ? ($stop['stop_at'] ?? null) : ($stop->stop_at ?? null);
                                    $titleValue = is_array($stop) ? ($stop['title'] ?? null) : ($stop->title ?? null); // Renamed to avoid conflict
                                    // Format time correctly from DB or old input
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
                                                {{-- Ensure $districtsByProvince is available --}}
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
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="mb-1">Tên điểm dừng (Nếu có)</label>
                                            <input type="text" name="stops[{{ $index }}][title]" class="form-control"
                                                   placeholder="Ví dụ: Bến xe Giáp Bát" value="{{ $titleValue }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-0">
                                            <label class="mb-1">Giờ dừng (HH:MM) <span
                                                    class="text-danger">*</span></label>
                                            <input type="time" name="stops[{{ $index }}][stop_at]" class="form-control"
                                                   value="{{ $stopAtFormatted }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pt-3">
                                        {{-- <label>&nbsp;</label> --}}
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

                        {{-- Specific Stop Errors --}}
                        @error('stops.*.district_id')
                        <div class="text-danger mt-2 small">{{ $message }}</div>
                        @enderror
                        @error('stops.*.stop_at')
                        <div class="text-danger mt-2 small">{{ $message }}</div>
                        @enderror
                        @error('stops.*.title')
                        <div class="text-danger mt-2 small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- === End Stops Management Section === --}}

            </div> {{-- End Main Card Body --}}

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
                <a href="{{ route('admin.bus_routes.index', ['route_id' => $selectedRouteId]) }}"
                   class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div> {{-- End Main Card --}}


    {{-- Hidden template for adding new stops --}}
    <div id="stop-template" style="display: none;">
        <div class="stop-item row mb-3 align-items-center border-bottom pb-3">
            <div class="col-md-5">
                <div class="form-group mb-0">
                    <label class="mb-1">Quận/Huyện/Địa điểm <span class="text-danger">*</span></label>
                    {{-- Use a unique class for template select --}}
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
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label class="mb-1">Tên điểm dừng (Nếu có)</label>
                    <input type="text" name="stops[__INDEX__][title]" class="form-control"
                           placeholder="Ví dụ: Ngã tư Sở">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label class="mb-1">Giờ dừng (HH:MM) <span class="text-danger">*</span></label>
                    <input type="time" name="stops[__INDEX__][stop_at]" class="form-control" required>
                </div>
            </div>
            <div class="col-md-1 pt-3">
                <button type="button" class="btn btn-danger btn-sm btn-remove-stop w-100 mt-1">Xoá</button>
            </div>
        </div>
    </div>
@endsection

{{-- Scripts for Select2 and dynamic stops --}}
@push('scripts')
    {{-- Ensure Select2 CSS/JS are included in your main layout or pushed once --}}
    {{-- @pushonce('styles')
        <link rel="stylesheet" href="{{asset("/admin/plugins/select2/css/select2.min.css")}}">
        <link rel="stylesheet" href="{{asset("/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css")}}">
    @endpushonce
    @pushonce('scripts')
        <script src="{{asset("/admin/plugins/select2/js/select2.full.min.js")}}"></script>
    @endpushonce --}}

    <script>
        $(document).ready(function () {
            // Initial index based on displayed items (old input or existing data)
            let stopIndex = {{ count($stopsToDisplay) }};
            const stopsContainer = $('#stops-container');
            const stopTemplateHtml = $('#stop-template').html(); // Get template HTML once

            // Function to initialize Select2 on a given element
            function initializeSelect2(element) {
                $(element).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: '-- Chọn địa điểm --'
                });
            }

            // Initialize Select2 for existing elements
            $('.select2-district').each(function () {
                initializeSelect2(this);
            });

            // Add Stop Button Click
            $('#btn-add-stop').on('click', function () {
                // Replace placeholder index in template HTML
                let newStopHtml = stopTemplateHtml.replace(/__INDEX__/g, stopIndex);
                let $newStopElement = $(newStopHtml); // Create jQuery object

                stopsContainer.append($newStopElement);

                // Find the select element within the newly added item and initialize Select2
                let newSelect = $newStopElement.find('.select2-district-template');
                initializeSelect2(newSelect);
                // Remove template class after initialization
                newSelect.removeClass('select2-district-template').addClass('select2-district');

                stopIndex++; // Increment index for the next item
            });

            // Remove Stop Button Click (using event delegation)
            stopsContainer.on('click', '.btn-remove-stop', function () {
                $(this).closest('.stop-item').remove();
                // Note: Re-indexing is not strictly necessary as PHP handles non-sequential keys,
                // but if you needed it for JS logic, you'd implement it here.
            });
        });
    </script>
@endpush
