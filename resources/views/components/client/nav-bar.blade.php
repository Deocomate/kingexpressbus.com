@php
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = \App\Helpers\SystemHelper::mediaUrl(data_get($webProfile, 'logo_url'), \App\Helpers\SystemHelper::mediaUrl('/client/images/web information/logo.jpg'));
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
        ['code' => 'vi', 'label' => __('client.nav.languages.vi'), 'flag' => asset('/client/icons/vn-flag.svg')],
        ['code' => 'en', 'label' => __('client.nav.languages.en'), 'flag' => asset('/client/icons/en-flag.svg')],
    ];
    $currentLanguage = collect($languageOptions)->firstWhere('code', $currentLocale);
@endphp

<header
    x-data="{
        mobileOpen: false,
        languageOpen: false,
        accountOpen: false,
        soldOutAlertOpen: true,
        scrolled: false,
        init() {
            setTimeout(() => this.soldOutAlertOpen = false, 9000);
            const onScroll = () => { this.scrolled = window.scrollY > 24; };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }
    }"
    @keydown.escape.window="mobileOpen = false; languageOpen = false; accountOpen = false"
    :class="scrolled ? 'ksb-header--scrolled' : ''"
    class="ksb-header sticky top-0 z-[40] border-b border-amber-100/60 bg-white/95 shadow-sm backdrop-blur-xl transition-all duration-300">

    {{-- ─── Top utility bar (desktop only) ─────────────────────────────────── --}}
    <div class="hidden border-b border-amber-100/50 bg-[#fffcf0] lg:block">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex h-10 items-center justify-between gap-4">

                {{-- Contact info --}}
                <div class="flex min-w-0 items-center gap-1 text-sm">
                    @if ($hotline)
                        <a href="tel:{{ $hotlineTel }}"
                            class="ksb-topbar-link group inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold text-emerald-700 transition-all duration-200 hover:bg-emerald-50/80">
                            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                <i class="fa-solid fa-phone text-[8px]"></i>
                            </span>
                            <span class="truncate">{{ $hotline }}</span>
                        </a>
                    @endif
                    @if ($email)
                        <a href="{{ $emailHref }}"
                            class="ksb-topbar-link inline-flex min-w-0 items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-500 transition-all duration-200 hover:bg-amber-50 hover:text-amber-700">
                            <i class="fa-regular fa-envelope text-[10px]"></i>
                            <span class="truncate">{{ $email }}</span>
                        </a>
                    @endif
                </div>

                {{-- Right actions --}}
                <div class="flex items-center gap-1.5">

                    {{-- Language switcher --}}
                    <div class="relative">
                        <button type="button"
                            @click="languageOpen = !languageOpen"
                            @click.outside="languageOpen = false"
                            :aria-expanded="languageOpen"
                            aria-haspopup="listbox"
                            class="ksb-pill-btn inline-flex h-8 items-center gap-1.5 rounded-lg border border-amber-100 bg-white px-2.5 text-xs font-semibold text-slate-600 transition-all duration-200 hover:border-amber-200 hover:bg-amber-50/60">
                            @if($currentLanguage)
                                <img src="{{ $currentLanguage['flag'] }}" alt="{{ $currentLanguage['label'] }}" class="h-3.5 w-3.5 rounded-full object-cover ring-1 ring-amber-100">
                            @endif
                            <span class="uppercase tracking-wide">{{ $currentLanguage['code'] ?? 'vi' }}</span>
                            <i class="fa-solid fa-chevron-down text-[8px] text-slate-400 transition-transform duration-200" :class="languageOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="languageOpen" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            role="listbox"
                            class="ksb-dropdown absolute right-0 top-full mt-2 w-40 origin-top-right rounded-xl border border-amber-100/80 bg-white p-1.5 shadow-card">
                            @foreach ($languageOptions as $language)
                                <a href="{{ route('client.locale.switch', ['locale' => $language['code'], 'redirect' => $localeRedirect]) }}"
                                    role="option"
                                    aria-selected="{{ $currentLocale === $language['code'] ? 'true' : 'false' }}"
                                    class="mb-0.5 flex items-center gap-2 rounded-lg px-3 py-2 text-xs transition-all duration-150 last:mb-0 {{ $currentLocale === $language['code'] ? 'bg-amber-50 font-semibold text-amber-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                                    <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}" class="h-4 w-4 rounded-full object-cover">
                                    {{ $language['label'] }}
                                    @if($currentLocale === $language['code'])
                                        <i class="fa-solid fa-check ml-auto text-[10px] text-amber-600"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Auth area --}}
                    @if ($isCustomer)
                        <div class="relative">
                            <button type="button"
                                @click="accountOpen = !accountOpen"
                                @click.outside="accountOpen = false"
                                :aria-expanded="accountOpen"
                                aria-haspopup="menu"
                                class="ksb-pill-btn inline-flex h-8 items-center gap-2 rounded-lg border border-amber-100 bg-white px-2.5 transition-all duration-200 hover:border-amber-200 hover:bg-amber-50/60">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-orange-500 text-[10px] font-bold text-white shadow-sm">
                                    {{ strtoupper(substr($authUser->name, 0, 1)) }}
                                </span>
                                <span class="max-w-[6rem] truncate text-xs font-semibold text-slate-700">{{ $authUser->name }}</span>
                                <i class="fa-solid fa-chevron-down text-[8px] text-slate-400 transition-transform duration-200" :class="accountOpen ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="accountOpen" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                role="menu"
                                class="ksb-dropdown absolute right-0 top-full mt-2 w-60 origin-top-right rounded-xl border border-amber-100/80 bg-white p-1.5 shadow-card">
                                <div class="mb-1.5 rounded-lg bg-gradient-to-br from-amber-50 to-orange-50/60 px-3 py-2.5">
                                    <p class="truncate text-xs font-bold text-slate-800">{{ $authUser->name }}</p>
                                    <p class="truncate text-[11px] text-slate-500">{{ $authUser->email ?? $authUser->phone }}</p>
                                </div>
                                <div class="space-y-0.5">
                                    @foreach ($customerLinks as $link)
                                        <a href="{{ $link['url'] }}" role="menuitem"
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs text-slate-600 transition-all duration-150 hover:bg-amber-50 hover:text-amber-700">
                                            <i class="{{ $link['icon'] }} w-3.5 text-center text-slate-400"></i>
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                                <form method="POST" action="{{ route('client.logout') }}" class="mt-1 border-t border-slate-100/80 pt-1">
                                    @csrf
                                    <button type="submit" role="menuitem"
                                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold text-rose-500 transition-all duration-150 hover:bg-rose-50 hover:text-rose-600">
                                        <i class="fa-solid fa-arrow-right-from-bracket w-3.5 text-center"></i>
                                        {{ __('client.nav.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('client.login') }}"
                            class="rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-600 transition-all duration-200 hover:bg-amber-50 hover:text-amber-700">
                            {{ __('client.nav.login') }}
                        </a>
                        <a href="{{ route('client.register') }}"
                            class="ksb-cta-sm rounded-lg bg-primary-600 px-3 py-1 text-xs font-bold text-white transition-all duration-200 hover:bg-primary-700 active:scale-95">
                            {{ __('client.nav.register') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Main nav bar ─────────────────────────────────────────────────────── --}}
    <div class="container mx-auto max-w-7xl px-4">
        <div class="flex items-center justify-between gap-4 transition-all duration-300"
            :class="scrolled ? 'h-16' : 'h-20'">

            {{-- Brand --}}
            <a href="{{ route('client.home') }}"
                class="group inline-flex shrink-0 items-center gap-3"
                aria-label="{{ __('client.nav.home_aria') }}">
                <div class="relative">
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}"
                        class="rounded-xl object-cover shadow-soft ring-1 ring-amber-200/60 transition-all duration-300 group-hover:ring-amber-300/80"
                        :class="scrolled ? 'h-9 w-9' : 'h-11 w-11'">
                    <span class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-400 shadow-sm"></span>
                </div>
                <div class="hidden sm:block">
                    <span class="block text-sm font-extrabold tracking-tight text-slate-800 transition-all duration-300 lg:text-base"
                        :class="scrolled ? 'text-sm' : ''">
                        {{ $brandTitle }}
                    </span>
                    <span class="block text-[10px] font-medium uppercase tracking-widest text-amber-600/80">Express Bus</span>
                </div>
            </a>

            {{-- Desktop navigation --}}
            <nav class="hidden min-w-0 flex-1 items-center justify-center gap-0.5 xl:flex" aria-label="Main navigation">
                @foreach ($mainMenu ?? [] as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $hasChildren = !empty($item->children);
                    @endphp
                    @if (!$hasChildren)
                        <a href="{{ url($item->url) }}"
                            class="ksb-nav-link relative whitespace-nowrap rounded-xl px-4 py-2 text-sm font-semibold transition-all duration-200 {{ $isActive ? 'bg-amber-50 text-amber-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                            {{ $item->name }}
                            @if ($isActive)
                                <span class="absolute bottom-1 left-1/2 h-0.5 w-4 -translate-x-1/2 rounded-full bg-amber-500"></span>
                            @endif
                        </a>
                    @else
                        <div class="relative" x-data="{ open: false }"
                            @mouseenter="open = true" @mouseleave="open = false"
                            @focusin="open = true" @focusout="open = false"
                            @keydown.escape.stop="open = false">
                            <a href="{{ url($item->url) }}"
                                :aria-expanded="open"
                                aria-haspopup="menu"
                                class="ksb-nav-link relative inline-flex items-center gap-1.5 whitespace-nowrap rounded-xl px-4 py-2 text-sm font-semibold transition-all duration-200 {{ $isActive || ($item->isParentOfActive ?? false) ? 'bg-amber-50 text-amber-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                                {{ $item->name }}
                                <i class="fa-solid fa-chevron-down text-[9px] opacity-60 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                @if ($isActive || ($item->isParentOfActive ?? false))
                                    <span class="absolute bottom-1 left-1/2 h-0.5 w-4 -translate-x-1/2 rounded-full bg-amber-500"></span>
                                @endif
                            </a>

                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                role="menu"
                                class="ksb-dropdown absolute left-0 top-full mt-1.5 w-60 origin-top-left rounded-xl border border-amber-100/60 bg-white p-1.5 shadow-card">
                                @foreach ($item->children as $child)
                                    <a href="{{ url($child->url) }}"
                                        role="menuitem"
                                        class="mb-0.5 flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-all duration-150 last:mb-0 {{ ($child->isActive ?? false) ? 'bg-amber-50 font-semibold text-amber-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-amber-100/80">
                                            <i class="fa-solid fa-location-dot text-[9px] text-amber-600"></i>
                                        </span>
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            {{-- Desktop CTA --}}
            <div class="hidden xl:flex xl:shrink-0">
                <a href="{{ route('client.routes.index') }}"
                    class="ksb-cta-btn group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-amber-500 px-5 py-2.5 text-sm font-bold text-white shadow-soft transition-all duration-200 hover:from-primary-700 hover:to-amber-600 hover:shadow-md active:scale-95">
                    <i class="fa-solid fa-ticket transition-transform duration-200 group-hover:-rotate-12"></i>
                    {{ __('client.routes.index.cta_button') }}
                </a>
            </div>

            {{-- Mobile hamburger --}}
            <button type="button"
                @click="mobileOpen = true"
                :aria-expanded="mobileOpen"
                aria-controls="client-mobile-nav"
                aria-label="{{ __('client.nav.open_menu') }}"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-amber-100 bg-white text-slate-700 transition-all duration-200 hover:bg-amber-50 active:scale-95 xl:hidden">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
        </div>
    </div>

    {{-- ─── Sold-out toast alert ─────────────────────────────────────────────── --}}
    <div x-show="soldOutAlertOpen" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-4"
        class="ksb-alert fixed right-4 top-20 z-[80] hidden w-[calc(100vw-2rem)] max-w-xs rounded-2xl border border-amber-200/60 bg-white p-4 shadow-card md:block md:right-6 lg:top-24"
        role="alert"
        aria-live="polite">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 text-amber-600">
                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
            </span>
            <p class="flex-1 text-sm font-medium leading-snug text-slate-700">{{ __('client.layout.warning_holiday_ticket') }}</p>
            <button type="button"
                @click="soldOutAlertOpen = false"
                aria-label="{{ __('client.layout.close_warning') }}"
                class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-slate-400 transition-all duration-150 hover:bg-slate-100 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
        {{-- Auto-close progress bar --}}
        <div class="mt-3 h-0.5 w-full overflow-hidden rounded-full bg-amber-100">
            <div class="h-full rounded-full bg-amber-400 origin-left animate-[shrink_9s_linear_forwards]"
                style="animation: ksb-progress-shrink 9s linear forwards;"></div>
        </div>
    </div>

    {{-- ─── Mobile drawer backdrop ───────────────────────────────────────────── --}}
    <div x-show="mobileOpen" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="ksb-drawer-backdrop fixed inset-0 z-[60] min-h-dvh bg-slate-900/40 backdrop-blur-[2px] xl:hidden"
        @click="mobileOpen = false"
        aria-hidden="true">
    </div>

    {{-- ─── Mobile drawer ────────────────────────────────────────────────────── --}}
    <aside x-show="mobileOpen" x-cloak
        x-transition:enter="transition ease-[cubic-bezier(0.32,0.72,0,1)] duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-250"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        id="client-mobile-nav"
        class="ksb-drawer fixed right-0 top-0 z-[70] flex min-h-dvh w-[88vw] max-w-sm flex-col overflow-y-auto bg-white shadow-card xl:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('client.nav.open_menu') }}">

        {{-- Drawer header --}}
        <div class="flex items-center justify-between border-b border-slate-100/80 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}"
                        class="h-9 w-9 rounded-xl object-cover ring-1 ring-amber-200/60">
                    <span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full border-2 border-white bg-emerald-400"></span>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $brandTitle }}</p>
                    <p class="text-[10px] font-medium uppercase tracking-widest text-amber-600/80">Express Bus</p>
                </div>
            </div>
            <button type="button"
                @click="mobileOpen = false"
                aria-label="{{ __('client.nav.close_menu') }}"
                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200/80 text-slate-500 transition-all duration-200 hover:bg-slate-100 active:scale-95">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Drawer body --}}
        <div class="flex-1 overflow-y-auto px-4 py-4">

            {{-- Main menu --}}
            <nav class="space-y-1" aria-label="Mobile navigation">
                @foreach ($mainMenu ?? [] as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $hasChildren = !empty($item->children);
                    @endphp
                    @if (!$hasChildren)
                        <a href="{{ url($item->url) }}"
                            class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-semibold transition-all duration-200 {{ $isActive ? 'bg-amber-50 text-amber-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-800' }}">
                            {{ $item->name }}
                            @if ($isActive)
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            @endif
                        </a>
                    @else
                        <div x-data="{ open: {{ ($item->isParentOfActive ?? false) ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open"
                                :aria-expanded="open"
                                class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-semibold transition-all duration-200 {{ ($item->isParentOfActive ?? false) ? 'bg-amber-50 text-amber-700' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-800' }}">
                                {{ $item->name }}
                                <i class="fa-solid fa-chevron-down text-xs opacity-50 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="mt-1 space-y-0.5 rounded-xl bg-slate-50/60 p-2">
                                @foreach ($item->children as $child)
                                    <a href="{{ url($child->url) }}"
                                        class="flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm transition-all duration-150 {{ ($child->isActive ?? false) ? 'bg-white font-semibold text-amber-700 shadow-sm' : 'text-slate-600 hover:bg-white hover:text-slate-800 hover:shadow-sm' }}">
                                        <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-md bg-amber-100/80">
                                            <i class="fa-solid fa-location-dot text-[8px] text-amber-600"></i>
                                        </span>
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            {{-- Primary CTA --}}
            <div class="mt-5">
                <a href="{{ route('client.routes.index') }}"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-amber-500 py-3.5 text-sm font-bold text-white shadow-soft transition-all duration-200 hover:from-primary-700 hover:to-amber-600 active:scale-[0.98]">
                    <i class="fa-solid fa-ticket"></i>
                    {{ __('client.routes.index.cta_button') }}
                </a>
            </div>

            {{-- Auth section --}}
            @if (!$isCustomer)
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <a href="{{ route('client.login') }}"
                        class="rounded-xl border border-slate-200 py-3 text-center text-sm font-semibold text-slate-700 transition-all duration-200 hover:bg-slate-50 active:scale-95">
                        {{ __('client.nav.login') }}
                    </a>
                    <a href="{{ route('client.register') }}"
                        class="rounded-xl border border-amber-200 bg-amber-50 py-3 text-center text-sm font-semibold text-amber-700 transition-all duration-200 hover:bg-amber-100 active:scale-95">
                        {{ __('client.nav.register') }}
                    </a>
                </div>
            @endif

            @if ($isCustomer)
                <div class="mt-4 rounded-2xl border border-amber-100/80 bg-gradient-to-br from-amber-50/80 to-orange-50/40 p-4">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-orange-500 text-sm font-bold text-white shadow-sm">
                            {{ strtoupper(substr($authUser->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $authUser->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $authUser->email ?? $authUser->phone }}</p>
                        </div>
                    </div>
                    <div class="space-y-0.5">
                        @foreach ($customerLinks as $link)
                            <a href="{{ $link['url'] }}"
                                class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition-all duration-150 hover:bg-white hover:shadow-sm">
                                <i class="{{ $link['icon'] }} w-4 text-center text-amber-500"></i>
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('client.logout') }}" class="mt-2 border-t border-amber-100/80 pt-2">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold text-rose-500 transition-all duration-150 hover:bg-white hover:shadow-sm">
                            <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                            {{ __('client.nav.logout') }}
                        </button>
                    </form>
                </div>
            @endif

            {{-- Language switcher --}}
            <div class="mt-4">
                <p class="mb-2 px-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">{{ __('client.nav.language') ?? 'Ngôn ngữ' }}</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($languageOptions as $language)
                        <a href="{{ route('client.locale.switch', ['locale' => $language['code'], 'redirect' => $localeRedirect]) }}"
                            class="flex items-center justify-center gap-2 rounded-xl border py-2.5 text-sm font-semibold transition-all duration-200 {{ $currentLocale === $language['code'] ? 'border-amber-400 bg-amber-400 text-white' : 'border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                            <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}" class="h-4 w-4 rounded-full object-cover">
                            {{ strtoupper($language['code']) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Drawer footer: contact --}}
        <div class="border-t border-slate-100/80 px-4 py-4">
            @if ($hotline)
                <a href="tel:{{ $hotlineTel }}"
                    class="flex items-center justify-center gap-2.5 rounded-xl bg-emerald-50 py-3 text-sm font-bold text-emerald-700 transition-all duration-200 hover:bg-emerald-100 active:scale-[0.98]">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100">
                        <i class="fa-solid fa-phone text-xs text-emerald-600"></i>
                    </span>
                    {{ $hotline }}
                </a>
            @endif
            @if ($email)
                <a href="{{ $emailHref }}"
                    class="mt-2 flex items-center justify-center gap-2.5 rounded-xl bg-slate-50 py-3 text-sm font-semibold text-slate-600 transition-all duration-200 hover:bg-slate-100 active:scale-[0.98]">
                    <i class="fa-regular fa-envelope text-slate-400"></i>
                    {{ $email }}
                </a>
            @endif
        </div>
    </aside>
</header>

@once
    @push('styles')
    <style>
        @keyframes ksb-progress-shrink {
            from { transform: scaleX(1); }
            to   { transform: scaleX(0); }
        }

        .ksb-header--scrolled {
            box-shadow: 0 4px 24px -8px rgba(15, 23, 42, 0.12);
            border-bottom-color: rgba(251, 191, 36, 0.3);
        }

        /* Smooth focus-visible rings using brand color */
        .ksb-header a:focus-visible,
        .ksb-header button:focus-visible {
            outline: 2px solid #FF9B00;
            outline-offset: 2px;
            border-radius: 10px;
        }

        /* Reduce motion: disable scroll-shrink transition */
        @media (prefers-reduced-motion: reduce) {
            .ksb-header,
            .ksb-header * {
                transition-duration: 0ms !important;
                animation-duration: 0ms !important;
            }
        }
    </style>
    @endpush
@endonce
