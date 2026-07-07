@php
    $appName = config('app.name', 'King Express Bus');
    $defaultDescription = __('client.seo.default_description', [
        'default' =>
            'Đặt vé xe khách trực tuyến - Kết nối hàng nghìn chuyến xe chất lượng cao trên khắp Việt Nam. Đặt vé dễ dàng, thanh toán an toàn.',
    ]);
    $defaultKeywords = __('client.seo.default_keywords', [
        'default' => 'đặt vé xe khách, xe limousine, xe giường nằm, Hà Nội Sa Pa, King Express Bus, vé xe online',
    ]);

    $pageTitle = trim(strip_tags((string) ($title ?? data_get($webProfile, 'title', $appName))));
    $pageDescription = trim(
        strip_tags((string) ($description ?? data_get($webProfile, 'description', $defaultDescription))),
    );
    $pageKeywords = trim(strip_tags((string) ($keywords ?? $defaultKeywords)));
    $faviconUrl = \App\Helpers\SystemHelper::mediaUrl(
        $favicon ?? data_get($webProfile, 'favicon_url'),
        \App\Helpers\SystemHelper::mediaUrl('/assets/client/icons/logo.ico'),
    );
    $bodyClassName = trim($bodyClass ?? '') !== '' ? trim($bodyClass) : 'bg-page text-ink';

    $currentUrl = $canonicalUrl ?? url()->current();
    $baseUrl = url('/');
    $logoUrl = \App\Helpers\SystemHelper::mediaUrl(
        data_get($webProfile, 'logo_url'),
        \App\Helpers\SystemHelper::mediaUrl('/assets/client/images/web-information/logo.jpg'),
    );
    $shareImage = \App\Helpers\SystemHelper::mediaUrl($ogImage ?? data_get($webProfile, 'share_image_url'), $logoUrl);
    $shareImageAlt = $ogImageAlt ?? $pageTitle;

    $siteNameValue = trim(strip_tags((string) ($siteName ?? data_get($webProfile, 'profile_name', $appName))));
    $localeValue = $locale ?? str_replace('-', '_', app()->getLocale()) . '_VN';
    $robotsMeta = $robots ?? 'index, follow';
    $ogTypeValue = $ogType ?? 'website';

    $contactPhone = data_get($webProfile, 'hotline', data_get($webProfile, 'phone', ''));
    $contactAddress = data_get($webProfile, 'address', '');
    $facebookUrl = data_get($webProfile, 'facebook_url', '');
    $clientAssetVersion = '1.1.0';

    $authUser = auth()->user();
    $customerNavLinks = [];
    if ($authUser && ($authUser->role ?? null) === 'customer') {
        $customerNavLinks = [
            [
                'label' => __('client.layout.profile'),
                'url' => route('client.profile.index'),
                'icon' => 'fa-solid fa-user',
            ],
            [
                'label' => __('client.layout.my_bookings'),
                'url' => route('client.profile.index') . '#history',
                'icon' => 'fa-solid fa-ticket',
            ],
        ];
    }

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteNameValue,
        'url' => $baseUrl,
        'logo' => $logoUrl,
    ];

    if ($contactPhone) {
        $structuredData['contactPoint'] = [
            '@type' => 'ContactPoint',
            'telephone' => $contactPhone,
            'contactType' => 'customer service',
            'availableLanguage' => ['Vietnamese', 'English'],
        ];
    }

    if ($facebookUrl) {
        $structuredData['sameAs'] = [$facebookUrl];
    }

    $structuredData['address'] = [
        '@type' => 'PostalAddress',
        'addressCountry' => 'VN',
    ];

    if ($contactAddress) {
        $structuredData['address']['streetAddress'] = $contactAddress;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $pageTitle }}</title>
    <meta name="title" content="{{ $pageTitle }}">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="author" content="{{ $siteNameValue }}">
    <meta name="robots" content="{{ $robotsMeta }}">
    <meta name="theme-color" content="#FF9B00">
    <link rel="canonical" href="{{ $currentUrl }}">
    <link rel="alternate" hreflang="{{ app()->getLocale() }}" href="{{ $currentUrl }}">

    <meta property="og:type" content="{{ $ogTypeValue }}">
    <meta property="og:site_name" content="{{ $siteNameValue }}">
    <meta property="og:locale" content="{{ $localeValue }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:image:secure_url" content="{{ $shareImage }}">
    <meta property="og:image:alt" content="{{ $shareImageAlt }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
    <meta name="twitter:image:alt" content="{{ $shareImageAlt }}">

    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/x-icon">

    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="{{ $bodyClassName }} antialiased">
    {{-- HEADER --}}
    <x-client.nav-bar :web-profile="$webProfile" :main-menu="$mainMenu" :auth-user="$authUser" :customer-links="$customerNavLinks" />

    {{-- MAIN CONTENT --}}
    <main>
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <x-client.footer :web-profile="$webProfile" />

    <x-client.floating-contact :web-profile="$webProfile" />

    <script>
        window.__kingFlashMessages = {
            success: [@if (session('success')) '{{ addslashes(session('success')) }}' @endif],
            error: [
                @if (session('error'))
                    '{{ addslashes(session('error')) }}',
                @endif
                @foreach ($errors->all() as $error)
                    '{{ addslashes($error) }}',
                @endforeach
            ],
            warning: [@if (session('warning')) '{{ addslashes(session('warning')) }}' @endif],
            info: [@if (session('info')) '{{ addslashes(session('info')) }}' @endif],
        };
    </script>
    @stack('scripts')
</body>

</html>
