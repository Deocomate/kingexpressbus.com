@php
    // === SEO FALLBACK LOGIC ===
    // Priority: 1. Page-specific → 2. WebProfile → 3. Default

    $appName = config('app.name', 'King Express Bus');
    $defaultDescription = __('client.seo.default_description', ['default' => 'Đặt vé xe khách trực tuyến - Kết nối hàng nghìn chuyến xe chất lượng cao trên khắp Việt Nam. Đặt vé dễ dàng, thanh toán an toàn.']);
    $defaultKeywords = __('client.seo.default_keywords', ['default' => 'đặt vé xe khách, xe limousine, xe giường nằm, Hà Nội Sa Pa, King Express Bus, vé xe online']);

    // Title: page-specific → webProfile → app name
    $pageTitle = $title ?? data_get($webProfile, 'title', $appName);

    // Description: page-specific → webProfile → default
    $pageDescription = $description ?? data_get($webProfile, 'description', $defaultDescription);

    // Keywords: page-specific → default
    $pageKeywords = $keywords ?? $defaultKeywords;

    // Favicon: page-specific → webProfile → default
    $faviconUrl = $favicon ?? data_get($webProfile, 'favicon_url', '/client/icons/logo.ico');

    // Body class
    $bodyClassName = trim($bodyClass ?? '') !== '' ? trim($bodyClass) : 'bg-slate-50';

    // URLs
    $currentUrl = $canonicalUrl ?? url()->current();
    $baseUrl = url('/');

    // Images
    $logoUrl = data_get($webProfile, 'logo_url', '/client/images/web information/logo.jpg');
    $shareImage = $ogImage ?? data_get($webProfile, 'share_image_url', $logoUrl);
    $shareImageAlt = $ogImageAlt ?? $pageTitle;

    // Site info
    $siteNameValue = $siteName ?? data_get($webProfile, 'profile_name', $appName);
    $localeValue = $locale ?? (str_replace('-', '_', app()->getLocale()) . '_VN');
    $robotsMeta = $robots ?? 'index, follow';
    $ogTypeValue = $ogType ?? 'website';

    // Contact info for structured data
    $contactPhone = data_get($webProfile, 'hotline', data_get($webProfile, 'phone', ''));
    $contactEmail = data_get($webProfile, 'email', '');
    $contactAddress = data_get($webProfile, 'address', '');
    $facebookUrl = data_get($webProfile, 'facebook_url', '');

    // Auth user for nav
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
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Primary SEO Meta Tags --}}
    <title>{{ $pageTitle }}</title>
    <meta name="title" content="{{ $pageTitle }}">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="author" content="{{ $siteNameValue }}">
    <meta name="robots" content="{{ $robotsMeta }}">
    <link rel="canonical" href="{{ $currentUrl }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $ogTypeValue }}">
    <meta property="og:site_name" content="{{ $siteNameValue }}">
    <meta property="og:locale" content="{{ $localeValue }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="{{ asset($shareImage) }}">
    <meta property="og:image:secure_url" content="{{ asset($shareImage) }}">
    <meta property="og:image:alt" content="{{ $shareImageAlt }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@kingexpressbus">
    <meta name="twitter:creator" content="@kingexpressbus">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ asset($shareImage) }}">
    <meta name="twitter:image:alt" content="{{ $shareImageAlt }}">

    {{-- Additional SEO Meta --}}
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#1565C0">
    <meta name="msapplication-TileColor" content="#1565C0">
    <link rel="alternate" hreflang="{{ app()->getLocale() }}" href="{{ $currentUrl }}">

    {{-- Favicon --}}
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/x-icon">

    {{-- JSON-LD Structured Data --}}
    @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteNameValue,
            'url' => $baseUrl,
            'logo' => asset($logoUrl),
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
    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- External Resources --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#e8f0fe', 100: '#c5d9fc', 200: '#9ebef9', 300: '#6fa0f5', 400: '#4a87f0', 500: '#1E88E5', 600: '#1565C0', 700: '#0d47a1', 800: '#0a3880', 900: '#072a60' },
                        accent: { 50: '#fff8e1', 100: '#ffecb3', 200: '#ffe082', 300: '#ffd54f', 400: '#ffca28', 500: '#D97706', 600: '#B45309', 700: '#92400E', 800: '#78350F', 900: '#451a03' },
                        neutral: { 50: '#F8F9FA', 100: '#F1F3F5', 200: '#E9ECEF', 300: '#DEE2E6', 400: '#ADB5BD', 500: '#636E72', 600: '#495057', 700: '#343A40', 800: '#2D3436', 900: '#1A1D1E' },
                    },
                    fontFamily: {
                        sans: ['Inter', 'Be Vietnam Pro', 'system-ui', 'sans-serif'],
                    },
                    borderRadius: {
                        'sm': '4px',
                        'DEFAULT': '6px',
                        'md': '6px',
                        'lg': '8px',
                    },
                    boxShadow: {
                        'soft': '0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)',
                        'card': '0 2px 8px rgba(0,0,0,0.06)',
                        'dropdown': '0 4px 16px rgba(0,0,0,0.08)',
                        'elevated': '0 8px 24px rgba(0,0,0,0.08)',
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            font-family: 'Inter', 'Be Vietnam Pro', system-ui, sans-serif;
            color: #2D3436;
            -webkit-font-smoothing: antialiased;
        }

        #mobile-menu {
            transition: transform 0.2s ease;
        }

        .social-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .social-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            transition: opacity 0.2s ease;
        }

        .social-icon:hover {
            opacity: 0.85;
        }

        .messenger {
            background-color: #0099ff;
        }

        .zalo {
            background-color: #0068FF;
        }

        .hotline {
            background-color: #dc2626;
        }

        details[open]>summary i.fa-chevron-down {
            transform: rotate(180deg);
        }

        details>summary {
            list-style: none;
        }

        details>summary::-webkit-details-marker {
            display: none;
        }

        /* Base transition for interactive elements */
        a, button, [role="button"] {
            transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, opacity 0.2s ease;
        }
    </style>
    @stack('styles')
</head>

<body class="{{ $bodyClassName }}">
    <div x-data="{ show: true }" x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-x-2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-2"
        x-init="setTimeout(() => show = false, 10000)"
        class="fixed top-20 right-4 z-50 w-full max-w-sm overflow-hidden bg-white rounded-lg shadow-card border border-neutral-200 pointer-events-auto"
        style="display: none;">
        <div class="p-4">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-accent-500 text-lg mt-0.5"></i>
                <p class="text-sm font-medium text-neutral-800 flex-1">
                    {{ __('client.layout.warning_holiday_ticket') }}
                </p>
                <button type="button" @click="show = false"
                    class="text-neutral-400 hover:text-neutral-600 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>
    <x-client.nav-bar :web-profile="$webProfile" :main-menu="$mainMenu" :auth-user="$authUser"
        :customer-links="$customerLinks" />
    <main>
        {{ $slot }}
    </main>
    <x-client.footer :web-profile="$webProfile" />
    @if ($webProfile)
        <div class="social-float">
            @if (data_get($webProfile, 'facebook_url'))
                <a href="https://m.me/{{ basename(parse_url(data_get($webProfile, 'facebook_url'), PHP_URL_PATH) ?? '') }}"
                    target="_blank" class="social-icon messenger" aria-label="Messenger">
                    <i class="fab fa-facebook-messenger"></i>
                </a>
            @endif
            @if (data_get($webProfile, 'zalo_url'))
                <a href="{{ data_get($webProfile, 'zalo_url') }}" target="_blank" class="social-icon zalo" aria-label="Zalo">
                    <span class="font-bold">Za</span>
                </a>
            @endif
            @if (data_get($webProfile, 'hotline'))
                <a href="tel:{{ str_replace([' ', '.'], '', data_get($webProfile, 'hotline')) }}" class="social-icon hotline"
                    aria-label="Hotline">
                    <i class="fas fa-phone-alt"></i>
                </a>
            @endif
            @if (data_get($webProfile, 'whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', data_get($webProfile, 'whatsapp')) }}" target="_blank"
                    class="social-icon bg-green-500" aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            @endif
        </div>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');

            function toggleMenu() {
                mobileMenu.classList.toggle('-translate-x-full');
                mobileMenuOverlay.classList.toggle('hidden');
            }

            if (mobileMenuButton) mobileMenuButton.addEventListener('click', toggleMenu);
            if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', toggleMenu);
            if (closeMobileMenuButton) closeMobileMenuButton.addEventListener('click', toggleMenu);
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right'
            };
            @if (session('success'))
                toastr.success('{{ addslashes(session('success')) }}');
            @endif
            @if (session('error'))
                toastr.error('{{ addslashes(session('error')) }}');
            @endif
            @if (session('warning'))
                toastr.warning('{{ addslashes(session('warning')) }}');
            @endif
            @if (session('info'))
                toastr.info('{{ addslashes(session('info')) }}');
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error('{{ addslashes($error) }}');
                @endforeach
            @endif
    });
    </script>
    @stack('scripts')
</body>

</html>