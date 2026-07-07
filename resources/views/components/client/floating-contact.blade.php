@php
    $messengerPath = parse_url((string) data_get($webProfile, 'facebook_url'), PHP_URL_PATH);
    $messengerId = $messengerPath ? trim((string) basename($messengerPath)) : '';
@endphp

@if ($webProfile)
    <div class="kx-floating-contact fixed bottom-6 right-4 hidden flex-col gap-3 md:right-6 md:flex">
        @if ($messengerId !== '')
            <a href="https://m.me/{{ $messengerId }}" target="_blank" rel="noopener noreferrer"
                class="kx-floating-btn"
                aria-label="Messenger">
                <i class="fab fa-facebook-messenger text-lg"></i>
            </a>
        @endif
        @if (data_get($webProfile, 'zalo_url'))
            <a href="{{ data_get($webProfile, 'zalo_url') }}" target="_blank" rel="noopener noreferrer"
                class="kx-floating-btn"
                aria-label="Zalo">
                <span class="text-xs font-extrabold tracking-tight">Za</span>
            </a>
        @endif
        @if (data_get($webProfile, 'hotline'))
            <a href="tel:{{ str_replace([' ', '.'], '', data_get($webProfile, 'hotline')) }}"
                class="kx-floating-btn kx-floating-btn--primary"
                aria-label="{{ __('client.nav.hotline') }}">
                <i class="fas fa-phone text-base"></i>
            </a>
        @endif
    </div>
@endif
