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
        \App\Helpers\SystemHelper::mediaUrl('/client/icons/logo.ico'),
    );
    $bodyClassName = trim($bodyClass ?? '') !== '' ? trim($bodyClass) : 'bg-[#F8FAFC] text-slate-800';

    $currentUrl = $canonicalUrl ?? url()->current();
    $baseUrl = url('/');
    $logoUrl = \App\Helpers\SystemHelper::mediaUrl(
        data_get($webProfile, 'logo_url'),
        \App\Helpers\SystemHelper::mediaUrl('/client/images/web information/logo.jpg'),
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
    $messengerPath = parse_url((string) $facebookUrl, PHP_URL_PATH);
    $messengerId = $messengerPath ? trim((string) basename($messengerPath)) : '';
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

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff9e6',
                            100: '#ffefbf',
                            200: '#ffe08a',
                            300: '#ffd156',
                            400: '#ffc43a',
                            500: '#FFC900',
                            600: '#FF9B00',
                            700: '#d97d00',
                            800: '#a85f00',
                            900: '#744100'
                        },
                        accent: {
                            DEFAULT: '#FFE100',
                            50: '#fffce5',
                            100: '#fff5b8',
                            500: '#FFE100',
                            600: '#FFC900',
                            700: '#b86100'
                        },
                        navy: {
                            50: '#eef6ff',
                            100: '#d9ebff',
                            700: '#0f2a44',
                            800: '#071a2e',
                            900: '#04111f',
                            950: '#020915'
                        },
                        pastel: '#EBE389',
                        pickup: '#10B981',
                        dropoff: '#EF4444'
                    },
                    fontFamily: {
                        sans: ['Be Vietnam Pro', 'Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        display: ['Manrope', 'Be Vietnam Pro', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        soft: '0 12px 28px -18px rgba(15, 23, 42, 0.28)',
                        card: '0 24px 60px -34px rgba(4, 17, 31, 0.38)',
                        lift: '0 18px 42px -28px rgba(4, 17, 31, 0.36)',
                    },
                    borderRadius: {
                        control: '12px',
                        panel: '18px',
                    },
                    zIndex: {
                        header: '40',
                        dropdown: '50',
                        drawer: '60',
                        modal: '70',
                        alert: '80',
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('/client/css/custom.css') }}?v={{ $clientAssetVersion }}">
    <script defer src="{{ asset('/client/js/client-ui.js') }}?v={{ $clientAssetVersion }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes ksb-float-up {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes ksb-soft-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(255, 155, 0, 0.16);
            }

            50% {
                box-shadow: 0 0 0 12px rgba(255, 155, 0, 0);
            }
        }

        .animate-ksb-float-up {
            animation: ksb-float-up .45s ease-out;
        }

        .animate-ksb-soft-pulse {
            animation: ksb-soft-pulse 2.4s ease-in-out infinite;
        }

        *::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        *::-webkit-scrollbar-track {
            background: #fff7d6;
        }

        *::-webkit-scrollbar-thumb {
            background: #FFC900;
            border-radius: 999px;
            border: 2px solid #fff7d6;
        }
    </style>
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

    @if ($webProfile)
        <div class="ksb-floating-contact fixed bottom-6 right-4 flex flex-col gap-3 md:right-6">
            @if ($messengerId !== '')
                <a href="https://m.me/{{ $messengerId }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0099FF] text-white shadow-soft transition hover:-translate-y-1 hover:shadow-lg"
                    aria-label="Messenger">
                    <i class="fab fa-facebook-messenger"></i>
                </a>
            @endif
            @if (data_get($webProfile, 'zalo_url'))
                <a href="{{ data_get($webProfile, 'zalo_url') }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0068FF] text-white shadow-soft transition hover:-translate-y-1 hover:shadow-lg"
                    aria-label="Zalo">
                    <span class="text-sm font-bold">Za</span>
                </a>
            @endif
            @if (data_get($webProfile, 'hotline'))
                <a href="tel:{{ str_replace([' ', '.'], '', data_get($webProfile, 'hotline')) }}"
                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500 text-white shadow-soft transition hover:-translate-y-1 hover:shadow-lg animate-ksb-soft-pulse"
                    aria-label="Hotline">
                    <i class="fas fa-phone-alt"></i>
                </a>
            @endif
        </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
