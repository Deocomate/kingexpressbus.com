{{-- Shared price breakdown: used by desktop sidebar and mobile bottom sheet. Updates via [data-summary] hooks. --}}
<div class="space-y-2 text-sm text-muted">
    <div class="flex items-center justify-between">
        <span>{{ __('client.booking.create.summary_base_price') }}</span>
        <span class="font-semibold text-ink">
            {{ $baseUnitPrice > 0 ? number_format($baseUnitPrice) . 'đ' : __('client.booking.create.summary_contact_price') }}
        </span>
    </div>
    @if ($globalSurchargeUnit > 0)
        <div class="flex items-center justify-between">
            <span>{{ __('client.booking.create.summary_global_surcharge') }}</span>
            <span class="font-semibold text-brand-700">+{{ number_format($globalSurchargeUnit) }}đ</span>
        </div>
    @endif
    @if ($routeSurchargeUnit > 0)
        <div class="flex items-center justify-between">
            <span>{{ __('client.booking.create.summary_route_surcharge') }}</span>
            <span class="font-semibold text-brand-700">+{{ number_format($routeSurchargeUnit) }}đ</span>
        </div>
    @endif
    <div class="flex items-center justify-between">
        <span>{{ __('client.booking.create.summary_price_per_ticket') }}</span>
        <span class="font-semibold text-ink">
            {{ $finalUnitPrice > 0 ? number_format($finalUnitPrice) . 'đ' : __('client.booking.create.summary_contact_price') }}
        </span>
    </div>
    <div class="flex items-center justify-between">
        <span>{{ __('client.booking.create.summary_quantity') }}</span>
        <span class="font-semibold text-ink" data-summary="quantity">1</span>
    </div>
    <div class="flex items-center justify-between">
        <span>{{ __('client.booking.create.summary_service_fee') }}</span>
        <span class="font-semibold text-green-600">{{ __('client.booking.create.summary_free') }}</span>
    </div>
    @if ($surchargeSnapshot)
        <p class="pt-1 text-xs leading-relaxed text-brand-800">
            {{ __('client.booking.create.summary_surcharge_note') }}: {{ $surchargeSnapshot }}
        </p>
    @endif
</div>

<div class="mt-3 flex items-center justify-between border-t border-line pt-3 text-lg font-bold text-ink">
    <span>{{ __('client.booking.create.summary_total') }}</span>
    <span class="ksb-price" data-summary="total">{{ number_format($finalUnitPrice) }}đ</span>
</div>
