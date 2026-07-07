@php
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = \App\Helpers\SystemHelper::mediaUrl(data_get($webProfile, 'logo_url'), \App\Helpers\SystemHelper::mediaUrl('/assets/client/images/web-information/logo.jpg'));
    $hotline = data_get($webProfile, 'hotline');
    $hotlineTel = $hotline ? preg_replace('/[^\d+]/', '', $hotline) : '';
    $email = data_get($webProfile, 'email');
    $emailHref = $email ? ('mailto:' . $email) : '';

    $authUser = $authUser ?? null;
    $isCustomer = $authUser && ($authUser->role ?? null) === 'customer';
    $customerLinks = $customerLinks ?? [];
    $localeRedirect = request()->getRequestUri();

    $currentLocale = app()->getLocale();
    $languageOptions = [
        ['code' => 'vi', 'label' => __('client.nav.languages.vi'), 'flag' => asset('/assets/client/icons/vn-flag.svg')],
        ['code' => 'en', 'label' => __('client.nav.languages.en'), 'flag' => asset('/assets/client/icons/en-flag.svg')],
    ];
    $currentLanguage = collect($languageOptions)->firstWhere('code', $currentLocale);
@endphp

<header
    x-data="{
        mobileOpen: false,
        languageOpen: false,
        accountOpen: false,
        scrolled: false,
        init() {
            const onScroll = () => { this.scrolled = window.scrollY > 24; };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }
    }"
    @keydown.escape.window="mobileOpen = false; languageOpen = false; accountOpen = false"
    :class="scrolled ? 'kx-header--scrolled' : ''"
    class="kx-header sticky top-0 z-header border-b border-line-strong bg-white transition-shadow duration-300">

    {{-- Top utility bar: hotline, language, account (desktop only) --}}
    <div class="kx-header-top hidden border-b border-line-strong lg:block">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex h-9 items-center justify-end gap-2">
                @if ($hotline)
                    <a href="tel:{{ $hotlineTel }}"
                        class="kx-header-utility inline-flex max-w-[10rem] items-center gap-1.5 truncate rounded-sm px-2 py-1">
                        <i class="fa-solid fa-phone shrink-0 text-[10px] text-ink/50"></i>
                        <span class="truncate">{{ $hotline }}</span>
                    </a>
                    <span class="h-3 w-px bg-line-strong" aria-hidden="true"></span>
                @endif

                <div class="relative" @click.outside="languageOpen = false">
                    <button type="button"
                        @click="languageOpen = !languageOpen"
                        :aria-expanded="languageOpen"
                        aria-haspopup="listbox"
                        class="kx-header-utility inline-flex h-7 items-center gap-1 rounded-sm px-2">
                        @if($currentLanguage)
                            <img src="{{ $currentLanguage['flag'] }}" alt="{{ $currentLanguage['label'] }}" class="h-3.5 w-3.5 rounded-sm object-cover">
                        @endif
                        <span class="uppercase">{{ $currentLanguage['code'] ?? 'vi' }}</span>
                        <i class="fa-solid fa-chevron-down text-[7px] text-ink/40" :class="languageOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="languageOpen" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        role="listbox"
                        class="absolute right-0 top-full z-header-menu w-36 origin-top-right pt-1">
                        <div class="rounded-sm border border-line-strong bg-white p-1 shadow-card">
                        @foreach ($languageOptions as $language)
                            <a href="{{ route('client.locale.switch', ['locale' => $language['code'], 'redirect' => $localeRedirect]) }}"
                                role="option"
                                aria-selected="{{ $currentLocale === $language['code'] ? 'true' : 'false' }}"
                                class="mb-0.5 flex items-center gap-2 rounded-sm px-2.5 py-1.5 text-xs transition-colors last:mb-0 {{ $currentLocale === $language['code'] ? 'bg-brand-100 font-semibold text-ink' : 'text-ink/70 hover:bg-brand-50 hover:text-ink' }}">
                                <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}" class="h-4 w-4 rounded-sm object-cover">
                                {{ $language['label'] }}
                            </a>
                        @endforeach
                        </div>
                    </div>
                </div>

                <span class="h-3 w-px bg-line-strong" aria-hidden="true"></span>

                @if ($isCustomer)
                    <div class="relative" @click.outside="accountOpen = false">
                        <button type="button"
                            @click="accountOpen = !accountOpen"
                            :aria-expanded="accountOpen"
                            aria-haspopup="menu"
                            class="kx-header-utility inline-flex h-7 max-w-[9rem] items-center gap-1.5 rounded-sm px-2">
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-sm bg-ink text-[9px] font-bold text-white">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </span>
                            <span class="truncate">{{ $authUser->name }}</span>
                            <i class="fa-solid fa-chevron-down shrink-0 text-[7px] text-ink/40" :class="accountOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="accountOpen" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            role="menu"
                            class="absolute right-0 top-full z-header-menu w-52 origin-top-right pt-1">
                            <div class="rounded-sm border border-line-strong bg-white p-1 shadow-card">
                            <div class="mb-1 rounded-sm bg-brand-50 px-2.5 py-1.5">
                                <p class="truncate text-xs font-bold text-ink">{{ $authUser->name }}</p>
                                <p class="truncate text-[11px] text-ink/60">{{ $authUser->email ?? $authUser->phone }}</p>
                            </div>
                            @foreach ($customerLinks as $link)
                                <a href="{{ $link['url'] }}" role="menuitem"
                                    class="flex items-center gap-2 rounded-sm px-2.5 py-1.5 text-xs text-ink/75 hover:bg-brand-50 hover:text-ink">
                                    <i class="{{ $link['icon'] }} w-3.5 text-center text-ink/40"></i>
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                            <form method="POST" action="{{ route('client.logout') }}" class="mt-1 border-t border-line pt-1">
                                @csrf
                                <button type="submit" role="menuitem"
                                    class="flex w-full items-center gap-2 rounded-sm px-2.5 py-1.5 text-xs font-semibold text-ink/75 hover:bg-brand-50 hover:text-ink">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-3.5 text-center"></i>
                                    {{ __('client.nav.logout') }}
                                </button>
                            </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('client.login') }}"
                        class="kx-header-utility rounded-sm px-2 py-1">
                        {{ __('client.nav.login') }}
                    </a>
                    <a href="{{ route('client.register') }}"
                        class="kx-btn-primary-sm">
                        {{ __('client.nav.register') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Main bar: brand + navigation + primary CTA --}}
    <div class="container mx-auto max-w-7xl px-4">
        <div class="flex h-14 items-center gap-4 overflow-visible">

            <a href="{{ route('client.home') }}"
                class="inline-flex shrink-0 items-center gap-2"
                aria-label="{{ __('client.nav.home_aria') }}">
                <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}"
                    class="h-9 w-9 rounded-sm border border-line-strong object-cover">
                <span class="hidden max-w-[10rem] truncate text-sm font-extrabold tracking-tight text-ink sm:block md:max-w-[14rem]">
                    {{ $brandTitle }}
                </span>
            </a>

            <nav class="hidden min-w-0 flex-1 items-center justify-center gap-0.5 overflow-visible xl:flex" aria-label="{{ __('client.nav.aria_main') }}">
                @foreach ($mainMenu ?? [] as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $hasChildren = !empty($item->children);
                    @endphp
                    @if (!$hasChildren)
                        <a href="{{ url($item->url) }}"
                            class="kx-nav-link {{ $isActive ? 'kx-nav-link--active' : '' }}">
                            {{ $item->name }}
                        </a>
                    @else
                        <div class="relative shrink-0" x-data="{ open: false }"
                            @mouseenter="open = true"
                            @mouseleave="open = false"
                            @keydown.escape.stop="open = false">
                            <a href="{{ url($item->url) }}"
                                :aria-expanded="open"
                                aria-haspopup="menu"
                                class="kx-nav-link inline-flex items-center gap-1 {{ $isActive || ($item->isParentOfActive ?? false) ? 'kx-nav-link--active' : '' }}">
                                {{ $item->name }}
                                <i class="fa-solid fa-chevron-down text-[8px] text-ink/40" :class="open ? 'rotate-180' : ''"></i>
                            </a>

                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                role="menu"
                                class="absolute left-0 top-full z-header-menu w-52 pt-1">
                                <div class="rounded-sm border border-line-strong bg-white p-1 shadow-card">
                                @foreach ($item->children as $child)
                                    <a href="{{ url($child->url) }}" role="menuitem"
                                        class="flex items-center rounded-sm px-2.5 py-2 text-sm {{ ($child->isActive ?? false) ? 'bg-brand-100 font-semibold text-ink' : 'text-ink/75 hover:bg-brand-50 hover:text-ink' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="ml-auto flex shrink-0 items-center gap-2">
                <a href="{{ route('client.routes.index') }}"
                    class="kx-btn-primary hidden rounded-sm px-4 py-2 text-sm sm:inline-flex">
                    <i class="fa-solid fa-ticket"></i>
                    <span class="hidden md:inline">{{ __('client.routes.index.cta_button') }}</span>
                    <span class="md:hidden">{{ __('client.nav.search_bus') }}</span>
                </a>

                <button type="button"
                    @click="mobileOpen = true"
                    :aria-expanded="mobileOpen"
                    aria-controls="client-mobile-nav"
                    aria-label="{{ __('client.nav.open_menu') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-sm border border-line-strong bg-white text-ink hover:bg-brand-50 xl:hidden">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile drawer backdrop --}}
    <div x-show="mobileOpen" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-drawer min-h-dvh bg-ink/50 backdrop-blur-[1px] xl:hidden"
        @click="mobileOpen = false"
        aria-hidden="true">
    </div>

    {{-- Mobile drawer --}}
    <aside x-show="mobileOpen" x-cloak
        x-transition:enter="transition ease-[cubic-bezier(0.32,0.72,0,1)] duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-250"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        id="client-mobile-nav"
        class="fixed right-0 top-0 z-modal flex min-h-dvh w-[88vw] max-w-sm flex-col overflow-y-auto border-l border-line-strong bg-white shadow-card xl:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('client.nav.aria_mobile') }}">

        <div class="flex items-center justify-between border-b border-line-strong px-4 py-3">
            <div class="flex min-w-0 items-center gap-2.5">
                <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}"
                    class="h-9 w-9 shrink-0 rounded-sm border border-line-strong object-cover">
                <p class="truncate text-sm font-bold tracking-tight text-ink">{{ $brandTitle }}</p>
            </div>
            <button type="button"
                @click="mobileOpen = false"
                aria-label="{{ __('client.nav.close_menu') }}"
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-sm border border-line-strong text-ink/60 hover:bg-brand-50 hover:text-ink">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-3">
            <nav class="space-y-0.5" aria-label="{{ __('client.nav.aria_mobile') }}">
                @foreach ($mainMenu ?? [] as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $hasChildren = !empty($item->children);
                    @endphp
                    @if (!$hasChildren)
                        <a href="{{ url($item->url) }}"
                            class="flex items-center justify-between rounded-sm px-3 py-2.5 text-sm font-semibold {{ $isActive ? 'bg-brand-100 text-ink' : 'text-ink/80 hover:bg-brand-50 hover:text-ink' }}">
                            {{ $item->name }}
                        </a>
                    @else
                        <div x-data="{ open: {{ ($item->isParentOfActive ?? false) ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" :aria-expanded="open"
                                class="flex w-full items-center justify-between rounded-sm px-3 py-2.5 text-left text-sm font-semibold {{ ($item->isParentOfActive ?? false) ? 'bg-brand-100 text-ink' : 'text-ink/80 hover:bg-brand-50 hover:text-ink' }}">
                                {{ $item->name }}
                                <i class="fa-solid fa-chevron-down text-xs text-ink/40" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-cloak class="mt-0.5 space-y-0.5 rounded-sm border border-line bg-brand-50 p-1.5">
                                @foreach ($item->children as $child)
                                    <a href="{{ url($child->url) }}"
                                        class="block rounded-sm px-2.5 py-2 text-sm {{ ($child->isActive ?? false) ? 'bg-white font-semibold text-ink' : 'text-ink/75 hover:bg-white hover:text-ink' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="mt-4">
                <a href="{{ route('client.routes.index') }}"
                    class="kx-btn-primary flex w-full items-center justify-center gap-2 rounded-sm py-3 text-sm">
                    <i class="fa-solid fa-ticket"></i>
                    {{ __('client.routes.index.cta_button') }}
                </a>
            </div>

            @if (!$isCustomer)
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <a href="{{ route('client.login') }}"
                        class="rounded-sm border border-line-strong py-2.5 text-center text-sm font-semibold text-ink hover:bg-brand-50">
                        {{ __('client.nav.login') }}
                    </a>
                    <a href="{{ route('client.register') }}"
                        class="kx-btn-primary rounded-sm py-2.5 text-center text-sm">
                        {{ __('client.nav.register') }}
                    </a>
                </div>
            @endif

            @if ($isCustomer)
                <div class="mt-3 rounded-sm border border-line-strong bg-brand-50 p-3">
                    <div class="mb-2 flex items-center gap-2.5">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-sm bg-ink text-sm font-bold text-white">
                            {{ strtoupper(substr($authUser->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-ink">{{ $authUser->name }}</p>
                            <p class="truncate text-xs text-ink/60">{{ $authUser->email ?? $authUser->phone }}</p>
                        </div>
                    </div>
                    @foreach ($customerLinks as $link)
                        <a href="{{ $link['url'] }}"
                            class="flex items-center gap-2 rounded-sm px-2.5 py-2 text-sm text-ink/80 hover:bg-white hover:text-ink">
                            <i class="{{ $link['icon'] }} w-4 text-center text-ink/40"></i>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    <form method="POST" action="{{ route('client.logout') }}" class="mt-2 border-t border-line pt-2">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2 rounded-sm px-2.5 py-2 text-sm font-semibold text-ink/75 hover:bg-white hover:text-ink">
                            <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                            {{ __('client.nav.logout') }}
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-3 lg:hidden">
                <p class="mb-1.5 px-1 text-[10px] font-semibold uppercase tracking-widest text-ink/40">{{ __('client.nav.language') }}</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($languageOptions as $language)
                        <a href="{{ route('client.locale.switch', ['locale' => $language['code'], 'redirect' => $localeRedirect]) }}"
                            class="flex items-center justify-center gap-2 rounded-sm border py-2 text-sm font-semibold {{ $currentLocale === $language['code'] ? 'border-ink bg-brand-500 text-ink' : 'border-line-strong text-ink/75 hover:bg-brand-50 hover:text-ink' }}">
                            <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}" class="h-4 w-4 rounded-sm object-cover">
                            {{ strtoupper($language['code']) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($hotline)
            <div class="border-t border-line-strong px-3 py-3 lg:hidden">
                <a href="tel:{{ $hotlineTel }}"
                    class="flex items-center justify-center gap-2 rounded-sm border border-line-strong bg-brand-50 py-2.5 text-sm font-bold text-ink hover:bg-brand-100">
                    <i class="fa-solid fa-phone text-xs text-ink/50"></i>
                    {{ $hotline }}
                </a>
            </div>
        @endif
    </aside>
</header>

@once
    @push('styles')
    <style>
        .kx-header--scrolled {
            box-shadow: 0 4px 20px -10px rgba(4, 17, 31, 0.18);
        }

        .kx-header,
        .kx-header nav {
            overflow: visible;
        }

        .kx-header a:focus-visible,
        .kx-header button:focus-visible {
            outline: 2px solid #FF9B00;
            outline-offset: 2px;
            border-radius: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            .kx-header,
            .kx-header * {
                transition-duration: 0ms !important;
                animation-duration: 0ms !important;
            }
        }
    </style>
    @endpush
@endonce
