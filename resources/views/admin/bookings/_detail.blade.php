{{--
  Booking detail slide-over.
  Opened via Alpine event: $dispatch('open-booking-detail', { bookingId: N })
  The content is loaded via fetch and injected into the slide-over body.

  When used standalone (from show() controller) the $booking variable is available.
  When used inside index.blade.php, it loads detail via AJAX.
--}}
@if(isset($booking))
@php
    $statusColorMap = [
        'pending'   => 'warning',
        'confirmed' => 'success',
        'cancelled' => 'danger',
        'completed' => 'info',
    ];
    $methodLabel = $booking->payment_method?->getLabel() ?? '—';
    $badgeClasses = [
        'success'   => 'bg-green-100 text-green-800',
        'danger'    => 'bg-red-100 text-red-800',
        'warning'   => 'bg-yellow-100 text-yellow-800',
        'info'      => 'bg-blue-100 text-blue-800',
        'primary'   => 'bg-brand-100 text-brand-800',
        'secondary' => 'bg-gray-100 text-gray-700',
    ];
    $statusBadgeColor = $statusColorMap[$booking->status?->value] ?? 'secondary';
    $statusBadgeHtml = '<span class="inline-flex items-center font-medium rounded-full text-xs px-2 py-1 '.($badgeClasses[$statusBadgeColor] ?? $badgeClasses['secondary']).'">'
        .e($booking->status?->getLabel() ?? '—')
        .'</span>';

    $bookingInfoItems = [
        'Mã đặt vé' => new \Illuminate\Support\HtmlString(
            '<span class="font-mono font-bold text-base">'.e($booking->booking_code).'</span>'
        ),
        'Trạng thái' => new \Illuminate\Support\HtmlString($statusBadgeHtml),
        'Ngày đặt vé' => $booking->created_at?->format('d/m/Y H:i'),
        'Ngày đi' => $booking->booking_date?->format('d/m/Y'),
        'Giờ khởi hành' => $booking->trip?->start_time
            ? \Carbon\Carbon::parse($booking->trip->start_time)->format('H:i')
            : '—',
        'Thời gian xác nhận' => $booking->confirmed_at?->format('d/m/Y H:i') ?? '—',
    ];
    $customerItems = [
        'Tài khoản' => $booking->user?->name ?? '—',
        'Tên khách hàng' => $booking->customer_name,
        'Email' => $booking->customer_email ?? '—',
        'Số điện thoại' => $booking->customer_phone,
    ];
    $scheduleItems = [
        'Tuyến đường' => $booking->trip?->route?->name ?? '—',
        'Xe' => $booking->trip?->bus?->name ?? '—',
        'Điểm đón' => $booking->pickupStop?->name ?? '—',
        'Điểm trả' => $booking->dropoffStop?->name ?? '—',
    ];
    $paymentItems = [
        'Số lượng vé' => $booking->quantity,
        'Tổng tiền' => number_format($booking->total_price, 0, ',', '.').' ₫',
        'Phương thức thanh toán' => $methodLabel,
        'Trạng thái thanh toán' => $booking->payment_status?->getLabel() ?? '—',
    ];
    $metaItems = [
        'Ngày cập nhật' => $booking->updated_at?->format('d/m/Y H:i'),
    ];
@endphp

<div class="space-y-6">
    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Thông tin đặt vé</h3>
        <x-admin::display.detail-list :columns="2" :items="$bookingInfoItems" />
    </div>

    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Khách hàng</h3>
        <x-admin::display.detail-list :columns="2" :items="$customerItems" />
    </div>

    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Lịch trình</h3>
        <x-admin::display.detail-list :columns="2" :items="$scheduleItems" />
    </div>

    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Thanh toán</h3>
        <x-admin::display.detail-list :columns="2" :items="$paymentItems" />
        @if($booking->payment_transaction_id)
        <div class="mt-2">
            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Mã giao dịch</dt>
            <dd class="mt-0.5 text-sm font-mono text-gray-900">{{ $booking->payment_transaction_id }}</dd>
        </div>
        @endif
    </div>

    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Chi tiết giá</h3>
        <div class="grid grid-cols-3 gap-x-6 gap-y-3 text-sm">
            @foreach([
                'Giá gốc/vé'        => $booking->base_unit_price,
                'Phụ thu chung/vé'  => $booking->global_surcharge_unit,
                'Phụ thu tuyến/vé'  => $booking->route_surcharge_unit,
                'Giá cuối/vé'       => $booking->final_unit_price,
                'Tổng phụ thu'      => $booking->total_surcharge_amount,
            ] as $label => $amount)
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</dt>
                <dd class="mt-0.5 text-sm text-gray-900">{{ number_format($amount, 0, ',', '.') }} ₫</dd>
            </div>
            @endforeach
        </div>
        @if($booking->surcharge_reason_snapshot)
        <div class="mt-3">
            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lý do phụ thu</dt>
            <dd class="mt-0.5 text-sm text-gray-900">{{ $booking->surcharge_reason_snapshot }}</dd>
        </div>
        @endif
    </div>

    @if($booking->notes)
    <div>
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Ghi chú</h3>
        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $booking->notes }}</p>
    </div>
    @endif

    <div class="border-t border-gray-100 pt-3">
        <x-admin::display.detail-list :columns="2" :items="$metaItems" />
    </div>
</div>

@else
{{-- Inline slide-over shell for the index page (content loaded via AJAX) --}}
<div
    x-data="{
        open: false,
        loading: false,
        content: '',
        trigger: null,
        close() {
            this.open = false;
            this.$nextTick(() => this.trigger?.focus?.());
        },
        init() {
            window.addEventListener('open-booking-detail', (e) => {
                this.trigger = document.activeElement;
                this.open = true;
                this.loadDetail(e.detail.bookingId);
                this.$nextTick(() => this.$refs.panel?.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex=\'-1\'])')?.focus());
            });
        },
        async loadDetail(id) {
            this.loading = true;
            this.content = '';
            try {
                const url = @js(route('admin.bookings.show', ['booking' => '__ID__'])).replace('__ID__', id);
                const r = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
                });
                this.content = await r.text();
            } finally {
                this.loading = false;
            }
        }
    }"
    x-on:close-slide-over.window="close()"
    class="relative z-40"
    role="dialog"
    aria-modal="true"
    aria-labelledby="booking-detail-title"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-black/30"
        x-cloak
    ></div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-2xl w-full">
                <div
                    x-ref="panel"
                    x-show="open"
                    x-trap.noscroll="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-full bg-white shadow-xl flex flex-col"
                    x-cloak
                >
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h2 id="booking-detail-title" class="text-base font-semibold text-gray-900">Chi tiết đặt vé</h2>
                        <button type="button" @click="close()"
                            class="rounded p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                            aria-label="Đóng">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        <div x-show="loading" class="py-12 text-center text-sm text-gray-400">Đang tải...</div>
                        <div x-show="!loading" x-html="content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
