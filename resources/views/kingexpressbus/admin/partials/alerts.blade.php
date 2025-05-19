{{-- Hiển thị thông báo thành công --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Hiển thị thông báo lỗi --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Hiển thị thông báo cảnh báo --}}
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Hiển thị thông báo thông tin --}}
@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Bạn cũng có thể thêm phần hiển thị lỗi validation chung ở đây nếu muốn,
   mặc dù trong form createOrEdit bạn đã có phần hiển thị lỗi chi tiết rồi --}}
{{--
@if ($errors->any() && !request()->is('*/create') && !request()->is('*/edit'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Có lỗi xảy ra!</strong> Đã xảy ra lỗi trong quá trình xử lý.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
--}}
