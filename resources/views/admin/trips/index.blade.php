<x-admin.layout title="Quản lý Chuyến xe">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Chuyến xe</li>
    </x-slot:breadcrumb>

@push('styles')
    <style>
        .trip-manager {
            display: flex;
            gap: 20px;
        }

        .bus-list-wrapper, .route-list-wrapper {
            flex-basis: 30%;
            flex-shrink: 0;
            background-color: #f4f6f9;
            border-radius: 5px;
            padding: 15px;
            height: 80vh;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
        }

        .route-list-wrapper {
            flex-basis: 70%;
        }

        .manager-header {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .bus-card, .trip-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: .3rem;
            margin-bottom: 10px;
            padding: 10px 15px;
            cursor: grab;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
        }

        .bus-card:active, .trip-card:active {
            cursor: grabbing;
        }

        .bus-name, .trip-bus-name {
            font-weight: 500;
        }

        .bus-details {
            font-size: 0.8em;
            color: #6c757d;
        }

        .draggable-list {
            min-height: 100px;
            list-style: none;
            padding: 0;
        }

        .route-container {
            background: #ffffff;
            border: 1px solid #dcdcdc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .route-header {
            font-weight: bold;
            color: #17a2b8;
            margin-bottom: 10px;
        }

        .trip-card {
            border-left: 3px solid #28a745;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trip-card.inactive {
            border-left-color: #6c757d;
            opacity: 0.7;
        }

        .sortable-ghost {
            background: #e9ecef;
            border: 1px dashed #adb5bd;
            opacity: 0.7;
        }

        .placeholder-text {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            border: 2px dashed #ced4da;
            border-radius: 5px;
        }
    </style>
@endpush

    <div class="trip-manager">
        <!-- Cột danh sách xe -->
        <div class="bus-list-wrapper">
            <h5 class="manager-header"><i class="fas fa-bus"></i> Danh sách xe</h5>
            <ul id="bus-list" class="draggable-list">
                @forelse ($buses as $bus)
                    <li class="bus-card" data-bus-id="{{ $bus->id }}" data-bus-name="{{ $bus->name }}"
                        data-bus-model="{{ $bus->model_name }}">
                        <div class="bus-name">{{ $bus->name }}</div>
                        <div class="bus-details">{{ $bus->model_name }} - {{ $bus->seat_count }} ghế</div>
                    </li>
                @empty
                    <p class="text-muted">Chưa có xe nào. <a href="{{ route('admin.buses.index') }}">Thêm xe</a></p>
                @endforelse
            </ul>
        </div>

        <!-- Cột danh sách tuyến đường và chuyến xe -->
        <div class="route-list-wrapper">
            <h5 class="manager-header"><i class="fas fa-route"></i> Tuyến đường & Lịch chạy</h5>
            <div class="accordion" id="accordionProvinces">
                @forelse ($startProvinces as $provinceId => $provinceName)
                    <div class="card shadow-none">
                        <div class="card-header bg-light" id="heading-{{ $provinceId }}">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                        data-target="#collapse-{{ $provinceId }}">
                                    <i class="fas fa-map-marker-alt text-info mr-2"></i> Xuất phát từ
                                    <strong>{{ $provinceName }}</strong>
                                </button>
                            </h2>
                        </div>
                        <div id="collapse-{{ $provinceId }}" class="collapse {{ $loop->first ? 'show' : '' }}"
                             data-parent="#accordionProvinces">
                            <div class="card-body">
                                @if (isset($routesByProvince[$provinceId]))
                                    @foreach ($routesByProvince[$provinceId] as $route)
                                        <div class="route-container">
                                            <div class="route-header">
                                                {{ $route->name }}
                                                <small class="text-muted">({{ $route->start_province_name }} → {{ $route->end_province_name }})</small>
                                            </div>
                                            <ul class="draggable-list trip-list" data-route-id="{{ $route->id }}">
                                                @if (isset($tripsByRoute[$route->id]))
                                                    @foreach ($tripsByRoute[$route->id] as $trip)
                                                        <li class="trip-card {{ !$trip->is_active ? 'inactive' : '' }}"
                                                            data-trip-id="{{ $trip->id }}">
                                                            <div>
                                                                <div class="trip-bus-name">
                                                                    <i class="fas fa-clock text-muted"></i>
                                                                    {{ \Carbon\Carbon::parse($trip->start_time)->format('H:i') }}
                                                                    - {{ \Carbon\Carbon::parse($trip->end_time)->format('H:i') }}
                                                                    | <i class="fas fa-bus text-muted"></i> {{ $trip->bus_name }}
                                                                </div>
                                                                <div class="bus-details">
                                                                    Giá vé: {{ number_format($trip->price) }}đ
                                                                    @if (!$trip->is_active)
                                                                        <span class="badge badge-secondary ml-2">Tạm dừng</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="action-buttons">
                                                                <button class="btn btn-info btn-xs edit-btn"
                                                                        data-id="{{ $trip->id }}"><i
                                                                        class="fas fa-pencil-alt"></i></button>
                                                                <button class="btn btn-warning btn-xs toggle-btn"
                                                                        data-id="{{ $trip->id }}"
                                                                        title="{{ $trip->is_active ? 'Tạm dừng' : 'Kích hoạt' }}">
                                                                    <i class="fas {{ $trip->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                                </button>
                                                                <button class="btn btn-danger btn-xs delete-btn"
                                                                        data-id="{{ $trip->id }}"><i
                                                                        class="fas fa-trash"></i></button>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <div class="placeholder-text">Kéo xe vào đây để tạo chuyến</div>
                                                @endif
                                            </ul>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        Chưa có tuyến đường nào. <a href="{{ route('admin.routes.index') }}">Thêm tuyến đường</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal for Adding/Editing Trip -->
    <div class="modal fade" id="trip-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="trip-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="trip-modal-label">Tạo chuyến xe mới</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="trip_id" name="id">
                        <input type="hidden" id="bus_id" name="bus_id">
                        <input type="hidden" id="route_id" name="route_id">
                        <div class="form-group">
                            <label>Xe</label>
                            <input type="text" class="form-control" id="modal_bus_name" readonly>
                        </div>
                        <div class="form-group">
                            <label>Tuyến đường</label>
                            <input type="text" class="form-control" id="modal_route_name" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time">Giờ khởi hành <span class="text-danger">*</span></label>
                                    <div class="input-group date" id="starttimepicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                               data-target="#starttimepicker" id="start_time" name="start_time"
                                               required/>
                                        <div class="input-group-append" data-target="#starttimepicker"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time">Giờ đến (dự kiến) <span class="text-danger">*</span></label>
                                    <div class="input-group date" id="endtimepicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                               data-target="#endtimepicker" id="end_time" name="end_time" required/>
                                        <div class="input-group-append" data-target="#endtimepicker"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script>
        $(function () {
            const modal = $('#trip-modal');
            const form = $('#trip-form');
            let cleavePrice = null;

            // Initialize Time Pickers
            $('#starttimepicker, #endtimepicker').datetimepicker({
                format: 'HH:mm',
                icons: {time: 'far fa-clock'}
            });

            // Initialize Price Formatter
            cleavePrice = new Cleave('#price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });

            // --- SORTABLEJS LOGIC ---
            const busListEl = document.getElementById('bus-list');
            const tripLists = document.querySelectorAll('.trip-list');

            new Sortable(busListEl, {
                group: {name: 'trips', pull: 'clone', put: false},
                animation: 150,
                sort: false
            });

            tripLists.forEach(list => {
                new Sortable(list, {
                    group: 'trips',
                    animation: 150,
                    onAdd: function (evt) {
                        const itemEl = evt.item;
                        const routeId = $(evt.to).data('route-id');
                        const routeName = $(evt.to).prev('.route-header').text();

                        form[0].reset();
                        $('#trip_id').val('');
                        $('#bus_id').val($(itemEl).data('bus-id'));
                        $('#route_id').val(routeId);
                        $('#modal_bus_name').val($(itemEl).data('bus-name'));
                        $('#modal_route_name').val(routeName.trim());
                        $('#trip-modal-label').text('Tạo Chuyến xe mới');
                        modal.modal('show');

                        $(evt.to).find('.placeholder-text').hide();
                        $(itemEl).remove();
                    },
                    onEnd: function (evt) {
                        const order = Array.from(evt.target.children).map(item => $(item).data('trip-id'));
                        const routeId = $(evt.target).data('route-id');
                        $.post('{{ route("admin.trips.updateOrder") }}', {
                            order,
                            route_id: routeId
                        })
                            .done(res => toastr.success(res.message))
                            .fail(() => toastr.error('Lỗi server, không thể cập nhật thứ tự.'));
                    }
                });
            });

            // --- MODAL & FORM LOGIC ---
            function renderTripCard(trip) {
                const formattedPrice = new Intl.NumberFormat('vi-VN').format(trip.price);
                const startTime = trip.start_time.substring(0, 5);
                const endTime = trip.end_time.substring(0, 5);
                const isActive = trip.is_active;

                return `
                    <li class="trip-card ${!isActive ? 'inactive' : ''}" data-trip-id="${trip.id}">
                        <div>
                            <div class="trip-bus-name">
                                <i class="fas fa-clock text-muted"></i> ${startTime} - ${endTime} |
                                <i class="fas fa-bus text-muted"></i> ${trip.bus_name}
                            </div>
                            <div class="bus-details">
                                Giá vé: ${formattedPrice}đ
                                ${!isActive ? '<span class="badge badge-secondary ml-2">Tạm dừng</span>' : ''}
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-info btn-xs edit-btn" data-id="${trip.id}"><i class="fas fa-pencil-alt"></i></button>
                            <button class="btn btn-warning btn-xs toggle-btn" data-id="${trip.id}" title="${isActive ? 'Tạm dừng' : 'Kích hoạt'}">
                                <i class="fas ${isActive ? 'fa-pause' : 'fa-play'}"></i>
                            </button>
                            <button class="btn btn-danger btn-xs delete-btn" data-id="${trip.id}"><i class="fas fa-trash"></i></button>
                        </div>
                    </li>`;
            }

            form.on('submit', function (e) {
                e.preventDefault();
                form.find('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();

                const id = $('#trip_id').val();
                const url = id ? `{{ url('admin/trips') }}/${id}` : '{{ route("admin.trips.store") }}';
                const method = id ? 'PUT' : 'POST';
                let formData = $(this).serializeArray();

                const priceIndex = formData.findIndex(item => item.name === 'price');
                if (priceIndex > -1) {
                    formData[priceIndex].value = cleavePrice.getRawValue();
                }

                $.ajax({
                    url: url, method: method, data: $.param(formData),
                    success: res => {
                        if (res.success) {
                            modal.modal('hide');
                            toastr.success(res.message);
                            if (id) {
                                $(`li[data-trip-id="${id}"]`).replaceWith(renderTripCard(res.data));
                            } else {
                                const routeId = res.data.route_id;
                                $(`.trip-list[data-route-id="${routeId}"]`).append(renderTripCard(res.data));
                            }
                        }
                    },
                    error: xhr => {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, (key, value) => {
                                const field = $(`#${key}`);
                                field.addClass('is-invalid');
                                field.closest('.form-group').append(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                            });
                            toastr.error("Vui lòng kiểm tra lại thông tin");
                        } else {
                            toastr.error("Đã xảy ra lỗi server.");
                        }
                    }
                });
            });

            $('body').on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                $.get(`{{ url('admin/trips') }}/${id}`, res => {
                    if (res.success) {
                        const data = res.data;
                        const card = $(`li[data-trip-id="${id}"]`);
                        form[0].reset();
                        $('#trip_id').val(data.id);
                        $('#start_time').val(data.start_time.substring(0, 5));
                        $('#end_time').val(data.end_time.substring(0, 5));
                        cleavePrice.setRawValue(data.price);
                        $('#modal_bus_name').val(card.find('.trip-bus-name').text().split('|')[1].trim());
                        $('#modal_route_name').val(card.closest('.route-container').find('.route-header').text().trim());
                        $('#trip-modal-label').text('Cập nhật Chuyến xe');
                        modal.modal('show');
                    }
                });
            });

            $('body').on('click', '.toggle-btn', function () {
                const id = $(this).data('id');
                const btn = $(this);

                $.ajax({
                    url: `{{ url('admin/trips') }}/${id}/toggle-status`,
                    method: 'PATCH',
                    success: res => {
                        if (res.success) {
                            toastr.success(res.message);
                            const card = $(`li[data-trip-id="${id}"]`);
                            card.toggleClass('inactive');

                            if (res.is_active) {
                                btn.find('i').removeClass('fa-play').addClass('fa-pause');
                                btn.attr('title', 'Tạm dừng');
                                card.find('.badge-secondary').remove();
                            } else {
                                btn.find('i').removeClass('fa-pause').addClass('fa-play');
                                btn.attr('title', 'Kích hoạt');
                                card.find('.bus-details').append('<span class="badge badge-secondary ml-2">Tạm dừng</span>');
                            }
                        }
                    },
                    error: () => toastr.error('Đã xảy ra lỗi.')
                });
            });

            $('body').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Bạn chắc chắn muốn xóa chuyến xe này?',
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                    confirmButtonText: 'Vâng, xóa nó!', cancelButtonText: 'Hủy'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('admin/trips') }}/${id}`, method: 'DELETE',
                            success: res => {
                                if (res.success) {
                                    toastr.success(res.message);
                                    $(`li[data-trip-id="${id}"]`).fadeOut(500, function () {
                                        $(this).remove();
                                    });
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
</x-admin.layout>
