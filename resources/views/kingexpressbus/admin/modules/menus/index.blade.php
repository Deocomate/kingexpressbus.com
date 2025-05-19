@extends('kingexpressbus.admin.layouts.main')
@section('title','Quản lý Menu')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Menu</h3>
            <div class="card-tools">
                <button id="save-menu-order" class="btn btn-success btn-sm" style="display: none;">
                    <i class="fas fa-save"></i> Lưu thứ tự Menu
                </button>
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tạo Menu Mới
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('kingexpressbus.admin.partials.alerts') {{-- Hiển thị thông báo --}}

            <p class="text-muted">
                <i class="fas fa-info-circle"></i> Bạn có thể kéo thả các mục menu để thay đổi thứ tự.
                Kéo một menu vào bên phải một menu khác để đặt làm menu con.
                Nhấn nút "Lưu thứ tự Menu" sau khi hoàn tất.
            </p>

            {{-- Container cho Nestable --}}
            <div class="dd" id="menu-nestable">
                {{-- Kiểm tra nếu $menuTree không rỗng --}}
                @if(!empty($menuTree) && count($menuTree) > 0)
                    {{-- Render cây menu bằng cách gọi partial đệ quy --}}
                    <ol class="dd-list">
                        @foreach ($menuTree as $item)
                            @include('kingexpressbus.admin.modules.menus.menu_item', ['item' => $item])
                        @endforeach
                    </ol>
                @else
                    <div class="dd-empty">Chưa có menu nào.</div>
                @endif
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.css">
    <style>
        /* Thiết lập lại toàn bộ style cho Nestable */
        .dd {
            position: relative;
            display: block;
            margin: 0;
            padding: 0;
            max-width: 100%;
            list-style: none;
            font-size: 13px;
            line-height: 20px;
        }

        .dd-list {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dd-list .dd-list {
            padding-left: 30px;
        }

        .dd-collapsed .dd-list {
            display: none;
        }

        .dd-item {
            display: block;
            position: relative;
            margin: 5px 0px;
            padding: 0;
            min-height: 20px;
            font-size: 13px;
            line-height: 20px;
        }

        /* Style mới cho menu item */
        .dd-content {
            display: flex;
            align-items: center;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .dd-content:hover {
            border-color: #3c8dbc;
            box-shadow: 0 2px 5px rgba(60, 141, 188, 0.15);
        }

        /* Chỉ phần drag handle có cursor pointer */
        .dd-handle {
            margin-left: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 46px;
            cursor: move;
            color: #aaa;
            border-right: 1px solid #eee;
            padding: 0 10px;
            font-size: 16px;
        }

        .handle-icon {
            color: #888;
        }

        .dd-handle:hover .handle-icon {
            color: #3c8dbc;
        }

        /* Phần nội dung chính */
        .dd-text {
            flex-grow: 1;
            padding: 12px 15px;
            font-weight: 600;
            color: #333;
        }

        .menu-url {
            font-weight: normal;
            font-size: 0.85em;
            color: #777;
            margin-left: 8px;
            font-style: italic;
        }

        /* Actions */
        .menu-actions {
            display: flex;
            padding-right: 15px;
        }

        .menu-actions .btn {
            margin-left: 5px;
            padding: 3px 6px;
        }

        .menu-actions .btn-xs {
            font-size: 0.75rem;
        }

        /* Placeholder */
        .dd-placeholder {
            margin: 5px 0;
            padding: 0;
            min-height: 46px;
            background: #f9fbff;
            border: 2px dashed #bed2e8;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Dragel (khi đang kéo) */
        .dd-dragel {
            position: absolute;
            pointer-events: none;
            z-index: 9999;
        }

        .dd-dragel > .dd-item .dd-content {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }

        /* Nút mở rộng/thu gọn */
        .dd-item > button {
            display: block;
            position: relative;
            float: left;
            width: 25px;
            height: 20px;
            margin: 13px 0;
            padding: 0;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 0;
            background: transparent;
            font-size: 12px;
            line-height: 1;
            text-align: center;
            z-index: 10;
        }

        .dd-item > button:before {
            content: '+';
            display: block;
            position: absolute;
            width: 100%;
            text-align: center;
            text-indent: 0;
            font-weight: bold;
        }

        .dd-item > button[data-action="collapse"]:before {
            content: '-';
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.js"></script>

    <script>
        $(document).ready(function () {
            // Chỉ kích hoạt drag & drop từ handle, không kéo được từ text/action buttons
            $('#menu-nestable').nestable({
                group: 1,
                maxDepth: 5,
                handleClass: 'dd-handle', // Chỉ kéo được từ phần dd-handle
                expandBtnHTML: '',
                collapseBtnHTML: ''
            }).on('change', function () {
                $('#save-menu-order').show();
            });

            // Mở rộng tất cả các mục khi trang tải xong
            $('.dd').nestable('expandAll');

            // Xử lý sự kiện click nút lưu
            $('#save-menu-order').on('click', function (e) {
                e.preventDefault();
                var nestableData = $('#menu-nestable').nestable('serialize');
                var button = $(this);

                // Hiển thị loading
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

                // Gửi AJAX request để lưu
                $.ajax({
                    url: '{{ route("admin.menus.updateOrder") }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        menuData: JSON.stringify(nestableData)
                    },
                    success: function (response) {
                        if (response.success) {
                            // Sử dụng toastr nếu đã tích hợp
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Thứ tự menu đã được cập nhật thành công!');
                            } else {
                                alert('Thứ tự menu đã được cập nhật thành công!');
                            }
                            button.hide();
                        } else {
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Có lỗi xảy ra: ' + (response.message || 'Không thể cập nhật thứ tự menu.'));
                            } else {
                                alert('Có lỗi xảy ra: ' + (response.message || 'Không thể cập nhật thứ tự menu.'));
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Lỗi kết nối hoặc lỗi server. Vui lòng thử lại.');
                        } else {
                            alert('Lỗi kết nối hoặc lỗi server. Vui lòng thử lại.');
                        }
                    },
                    complete: function () {
                        button.prop('disabled', false).html('<i class="fas fa-save"></i> Lưu thứ tự Menu');
                    }
                });
            });
        });
    </script>
@endpush
