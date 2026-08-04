@extends('admin.layouts.app')

@php
    $isEdit = $booking !== null;
    $title  = $isEdit ? 'Sửa đặt vé — '.$booking->booking_code : 'Tạo đặt vé';
@endphp

@section('title', $title)
@section('breadcrumb')
    <a href="{{ route('admin.bookings.index') }}" class="text-gray-500 hover:text-gray-700">Đặt vé</a>
    <span class="mx-2 text-gray-300">/</span>
    <span class="text-gray-700 font-medium">{{ $title }}</span>
@endsection

@section('content')
{{-- NOTE FOR MAINTAINERS:
     status and confirmed_at are intentionally absent from this form.
     Status changes must go through the confirm / cancel / complete action buttons on the list page.
     This ensures email notifications (BookingService) are always triggered.
     DO NOT add status to Booking::$fillable to work around this — use the action controllers instead.
--}}

<form
    method="POST"
    action="{{ $isEdit ? route('admin.bookings.update', $booking) : route('admin.bookings.store') }}"
    class="space-y-6"
>
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">{{ $title }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.bookings.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit"
                class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                {{ $isEdit ? 'Lưu thay đổi' : 'Tạo đặt vé' }}
            </button>
        </div>
    </div>

    @if($errors->any())
    <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Section 1: Thông tin đặt vé --}}
    <x-admin::form.section title="Thông tin đặt vé">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <x-admin::form.input
                name="booking_code"
                label="Mã đặt vé"
                :value="old('booking_code', $booking?->booking_code)"
                required
            />

            <x-admin::form.select-search
                name="user_id"
                label="Tài khoản khách hàng"
                source="users"
                :value="old('user_id', $booking?->user_id)"
                :valueText="old('user_id') ? null : $booking?->user?->name"
                placeholder="— Tìm tài khoản —"
            />

            <x-admin::form.select-search
                name="trip_id"
                label="Chuyến xe"
                source="trips"
                :value="old('trip_id', $booking?->trip_id)"
                :valueText="old('trip_id') ? null : ($booking?->trip ? \Carbon\Carbon::parse($booking->trip->start_time)->format('H:i').' — '.$booking->trip->route?->name : null)"
                placeholder="— Tìm chuyến xe —"
                required
            />

            <x-admin::form.date
                name="booking_date"
                label="Ngày đi"
                :value="old('booking_date', $booking?->booking_date?->format('Y-m-d'))"
                required
            />

            <x-admin::form.input
                name="customer_name"
                label="Tên khách hàng"
                :value="old('customer_name', $booking?->customer_name)"
                required
            />

            <x-admin::form.input
                name="customer_email"
                label="Email"
                type="email"
                :value="old('customer_email', $booking?->customer_email)"
            />

            <x-admin::form.input
                name="customer_phone"
                label="Số điện thoại"
                type="tel"
                :value="old('customer_phone', $booking?->customer_phone)"
                required
            />

            <x-admin::form.input
                name="quantity"
                label="Số lượng vé"
                type="number"
                :value="old('quantity', $booking?->quantity ?? 1)"
                required
            />

            <x-admin::form.select-search
                name="pickup_stop_id"
                label="Điểm đón"
                source="stops"
                :value="old('pickup_stop_id', $booking?->pickup_stop_id)"
                :valueText="old('pickup_stop_id') ? null : $booking?->pickupStop?->name"
                placeholder="— Tìm điểm đón —"
            />

            <x-admin::form.select-search
                name="dropoff_stop_id"
                label="Điểm trả"
                source="stops"
                :value="old('dropoff_stop_id', $booking?->dropoff_stop_id)"
                :valueText="old('dropoff_stop_id') ? null : $booking?->dropoffStop?->name"
                placeholder="— Tìm điểm trả —"
                required
            />

        </div>
    </x-admin::form.section>

    {{-- Section 2: Thanh toán --}}
    <x-admin::form.section title="Thanh toán"
        description="Trạng thái (status, confirmed_at) chỉ thay đổi qua các nút Xác nhận / Hủy / Hoàn thành trên trang danh sách.">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <x-admin::form.money
                name="total_price"
                label="Tổng tiền"
                :value="old('total_price', $booking?->total_price)"
                required
            />

            <x-admin::form.select
                name="payment_method"
                label="Phương thức thanh toán"
                :options="$paymentMethodOptions"
                :value="old('payment_method', $booking?->payment_method?->value ?? 'cash_on_pickup')"
                required
            />

            <x-admin::form.select
                name="payment_status"
                label="Trạng thái thanh toán"
                hint="Giá trị này được gán tường minh, không qua mass-assignment."
                :options="$paymentStatusOptions"
                :value="old('payment_status', $booking?->payment_status?->value)"
                placeholder="— Giữ nguyên —"
            />

            <x-admin::form.input
                name="payment_transaction_id"
                label="Mã giao dịch"
                hint="Gán tường minh, không qua mass-assignment."
                :value="old('payment_transaction_id', $booking?->payment_transaction_id)"
            />

            <div class="col-span-2">
                <x-admin::form.textarea
                    name="notes"
                    label="Ghi chú"
                    :value="old('notes', $booking?->notes)"
                    :rows="3"
                />
            </div>

        </div>
    </x-admin::form.section>

    {{-- Section 3: Chi tiết giá --}}
    <x-admin::form.section title="Chi tiết giá">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <x-admin::form.money
                name="base_unit_price"
                label="Giá gốc/vé"
                :value="old('base_unit_price', $booking?->base_unit_price ?? 0)"
                required
            />

            <x-admin::form.money
                name="global_surcharge_unit"
                label="Phụ thu chung/vé"
                :value="old('global_surcharge_unit', $booking?->global_surcharge_unit ?? 0)"
                required
            />

            <x-admin::form.money
                name="route_surcharge_unit"
                label="Phụ thu tuyến/vé"
                :value="old('route_surcharge_unit', $booking?->route_surcharge_unit ?? 0)"
                required
            />

            <x-admin::form.money
                name="final_unit_price"
                label="Giá cuối/vé"
                :value="old('final_unit_price', $booking?->final_unit_price ?? 0)"
                required
            />

            <x-admin::form.money
                name="total_surcharge_amount"
                label="Tổng phụ thu"
                :value="old('total_surcharge_amount', $booking?->total_surcharge_amount ?? 0)"
                required
            />

            <div class="col-span-2">
                <x-admin::form.textarea
                    name="surcharge_reason_snapshot"
                    label="Lý do phụ thu"
                    :value="old('surcharge_reason_snapshot', $booking?->surcharge_reason_snapshot)"
                    :rows="2"
                />
            </div>

        </div>
    </x-admin::form.section>

    {{-- Submit (bottom) --}}
    <div class="flex justify-end gap-2">
        <a href="{{ route('admin.bookings.index') }}"
           class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
            Hủy bỏ
        </a>
        <button type="submit"
            class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
            {{ $isEdit ? 'Lưu thay đổi' : 'Tạo đặt vé' }}
        </button>
    </div>

</form>
@endsection
