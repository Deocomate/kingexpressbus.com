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

<header x-data="{ mobileOpen: false, languageOpen: false, accountOpen: false, soldOutAlertOpen: true }"
    x-init="setTimeout(() => soldOutAlertOpen = false, 9000)"
    @keydown.escape.window="mobileOpen = false; languageOpen = false; accountOpen = false"
    class="sticky top-0 z-50 border-b border-amber-100/80 bg-white/90 shadow-sm backdrop-blur-xl" style="z-index: 9999;">
    <div class="hidden border-b border-amber-100/80 bg-[#fffdf5] lg:block">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex h-11 items-center justify-between gap-4 text-sm">
                <div class="flex min-w-0 items-center gap-2 text-slate-500">
                    @if ($hotline)
                        <a href="tel:{{ $hotlineTel }}"
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                            <i class="fa-solid fa-phone-volume text-[11px]"></i>
                            <span class="truncate">{{ $hotline }}</span>
                        </a>
                    @endif
                    @if ($email)
                        <a href="{{ $emailHref }}"
                            class="inline-flex min-w-0 items-center gap-1.5 rounded-lg px-2.5 py-1 text-sm font-semibold text-slate-600 transition hover:bg-amber-50 hover:text-primary-700">
                            <i class="fa-regular fa-envelope text-[11px]"></i>
                            <span class="truncate">{{ $email }}</span>
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative">
                        <button type="button" @click="languageOpen = !languageOpen" @click.outside="languageOpen = false"
                            :aria-expanded="languageOpen"
                            aria-haspopup="menu"
                            class="inline-flex h-9 items-center gap-2 rounded-lg border border-amber-100 bg-white px-2.5 text-sm font-semibold text-slate-600 transition hover:bg-amber-50">
                            @if($currentLanguage)
                                <img src="{{ $currentLanguage['flag'] }}" alt="{{ $currentLanguage['label'] }}" class="h-4 w-4 rounded-full object-cover">
                            @endif
                            <span class="uppercase">{{ $currentLanguage['code'] ?? 'vi' }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </button>
                        <div x-show="languageOpen" x-cloak x-transition.origin.top.right.duration.220ms
                            class="absolute right-0 top-full z-50 mt-2 w-44 rounded-2xl border border-amber-100 bg-white p-2 shadow-soft" style="z-index: 320;">
                            @foreach ($languageOptions as $language)
                                <a href="{{ route('client.locale.switch', ['locale' => $language['code'], 'redirect' => $localeRedirect]) }}"
                                    class="mb-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm transition last:mb-0 {{ $currentLocale === $language['code'] ? 'bg-primary-50 font-semibold text-primary-700' : 'text-slate-600 hover:bg-amber-50 hover:text-primary-700' }}">
                                    <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}" class="h-5 w-5 rounded-full object-cover">
                                    {{ $language['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if ($isCustomer)
                        <div class="relative">
                            <button type="button" @click="accountOpen = !accountOpen" @click.outside="accountOpen = false"
                                :aria-expanded="accountOpen"
                                aria-haspopup="menu"
                                class="inline-flex h-9 items-center gap-2 rounded-lg border border-amber-100 bg-white px-2.5 transition hover:bg-amber-50">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-primary-600 text-[11px] font-bold text-white">
                                    {{ strtoupper(substr($authUser->name, 0, 1)) }}
                                </span>
                                <span class="max-w-24 truncate text-sm font-semibold text-slate-700">{{ $authUser->name }}</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>
                            <div x-show="accountOpen" x-cloak x-transition.origin.top.right.duration.220ms
                                class="absolute right-0 top-full z-50 mt-2 w-64 rounded-2xl border border-amber-100 bg-white p-2 shadow-soft" style="z-index: 320;">
                                <div class="rounded-xl bg-slate-50 px-3 py-2">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $authUser->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $authUser->email ?? $authUser->phone }}</p>
                                </div>
                                <div class="mt-2 space-y-1">
                                    @foreach ($customerLinks as $link)
                                        <a href="{{ $link['url'] }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-amber-50 hover:text-primary-700">
                                            <i class="{{ $link['icon'] }} w-4 text-center"></i>
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                                <form method="POST" action="{{ route('client.logout') }}" class="mt-2 border-t border-slate-100 pt-2">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                        {{ __('client.nav.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('client.login') }}" class="rounded-lg px-2.5 py-1 text-sm font-semibold text-slate-600 transition hover:bg-amber-50 hover:text-primary-700">
                            {{ __('client.nav.login') }}
                        </a>
                        <a href="{{ route('client.register') }}" class="rounded-lg bg-primary-600 px-2.5 py-1 text-sm font-semibold text-white shadow-soft transition hover:bg-primary-700 active:scale-95">
                            {{ __('client.nav.register') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto max-w-7xl px-4">
        <div class="flex h-20 items-center justify-between gap-4">
            <a href="{{ route('client.home') }}" class="group inline-flex items-center gap-3" aria-label="{{ __('client.nav.home_aria') }}">
                <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-11 w-11 rounded-2xl object-cover shadow-soft ring-1 ring-amber-100">
                <span class="hidden text-base font-extrabold tracking-tight text-slate-800 sm:inline lg:text-lg">
                    {{ $brandTitle }}
                </span>
            </a>

            <nav class="hidden min-w-0 flex-1 items-center justify-center gap-1 xl:flex">
                @foreach ($mainMenu ?? [] as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $hasChildren = !empty($item->children);
                    @endphp
                    @if (!$hasChildren)
                        <a href="{{ url($item->url) }}"
                            class="whitespace-nowrap rounded-xl px-4 py-2 text-base font-semibold transition {{ $isActive ? 'bg-primary-100 text-primary-700' : 'text-slate-600 hover:bg-primary-50 hover:text-primary-700' }}">
                            {{ $item->name }}
                        </a>
                    @else
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                            @focusin="open = true" @focusout="open = false" @keydown.escape.stop="open = false">
                            <a href="{{ url($item->url) }}"
                                :aria-expanded="open"
                                aria-haspopup="menu"
                                class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-4 py-2 text-base font-semibold transition {{ $isActive || ($item->isParentOfActive ?? false) ? 'bg-primary-100 text-primary-700' : 'text-slate-600 hover:bg-primary-50 hover:text-primary-700' }}">
                                {{ $item->name }}
                                <i class="fa-solid fa-chevron-down text-[10px] transition" :class="open ? 'rotate-180' : ''"></i>
                            </a>
                            <div x-show="open" x-cloak x-transition.origin.top.left.duration.250ms
                                role="menu"
                                class="absolute left-0 top-full z-50 mt-2 w-64 rounded-2xl border border-amber-100 bg-white p-2 shadow-soft" style="z-index: 320;">
                                @foreach ($item->children as $child)
                                    <a href="{{ url($child->url) }}"
                                        class="mb-1 flex items-center gap-2 rounded-xl px-3 py-2 text-sm transition last:mb-0 {{ ($child->isActive ?? false) ? 'bg-primary-50 font-semibold text-primary-700' : 'text-slate-600 hover:bg-amber-50 hover:text-primary-700' }}">
                                        <i class="fa-solid fa-location-dot text-xs text-primary-600"></i>
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="hidden xl:flex">
                <a href="{{ route('client.routes.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-soft transition hover:bg-primary-700 active:scale-95">
                    <i class="fa-solid fa-ticket"></i>
                    {{ __('client.routes.index.cta_button') }}
                </a>
            </div>

            <button type="button" @click="mobileOpen = true" :aria-expanded="mobileOpen" aria-controls="client-mobile-nav"
                aria-label="{{ __('client.nav.open_menu') }}"
                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-amber-100 bg-white text-lg text-slate-700 xl:hidden">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    <div x-show="soldOutAlertOpen" x-cloak x-transition.opacity.duration.250ms x-transition.scale.origin.top.right.duration.250ms
        class="fixed right-4 top-[88px] z-50 w-[calc(100vw-2rem)] max-w-sm rounded-2xl border border-amber-100 bg-white p-4 shadow-soft" style="z-index: 310;">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex h-7 w-7 items-center justify-center rounded-xl bg-amber-100 text-primary-600">
                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
            </span>
            <p class="flex-1 text-sm font-medium text-slate-700">{{ __('client.layout.warning_holiday_ticket') }}</p>
            <button type="button" @click="soldOutAlertOpen = false" class="text-slate-400 transition hover:text-slate-700" aria-label="{{ __('client.layout.close_warning') }}">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak x-transition.opacity.duration.200ms class="fixed inset-0 z-50 bg-slate-900/50 xl:hidden h-screen"
        style="z-index: 100000;" @click="mobileOpen = false"></div>

    <aside x-show="mobileOpen" x-cloak x-transition:enter="transition duration-300" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        id="client-mobile-nav"
        class="fixed right-0 top-0 z-50 h-screen w-[88vw] max-w-sm overflow-y-auto bg-white p-5 xl:hidden" style="z-index: 100010;">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10 w-10 rounded-xl object-cover ring-1 ring-amber-100">
                <p class="text-sm font-bold text-slate-800">{{ $brandTitle }}</p>
            </div>
            <button type="button" @click="mobileOpen = false" aria-label="{{ __('client.nav.close_menu') }}"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="space-y-2">
            @foreach ($mainMenu ?? [] as $item)
                @php
                    $isActive = $item->isActive ?? false;
                    $hasChildren = !empty($item->children);
                @endphp
                @if (!$hasChildren)
                    <a href="{{ url($item->url) }}" class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold {{ $isActive ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-amber-50' }}">
                        {{ $item->name }}
                    </a>
                @else
                    <div x-data="{ open: {{ ($item->isParentOfActive ?? false) ? 'true' : 'false' }} }" class="rounded-2xl border border-slate-100">
                        <button type="button" @click="open = !open" class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-semibold text-slate-700">
                            {{ $item->name }}
                            <i class="fa-solid fa-chevron-down text-xs transition" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.duration.220ms class="space-y-1 px-2 pb-3">
                            @foreach ($item->children as $child)
                                <a href="{{ url($child->url) }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm {{ ($child->isActive ?? false) ? 'bg-primary-50 font-semibold text-primary-700' : 'text-slate-600 hover:bg-amber-50' }}">
                                    <i class="fa-solid fa-angle-right text-xs"></i>
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        @if (!$isCustomer)
            <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="{{ route('client.login') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700">
                    {{ __('client.nav.login') }}
                </a>
                <a href="{{ route('client.register') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-center text-sm font-semibold text-white active:scale-95">
                    {{ __('client.nav.register') }}
                </a>
            </div>
        @endif

        @if ($isCustomer)
            <div class="mt-6 rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                <div class="mb-3 flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">
                        {{ strtoupper(substr($authUser->name, 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $authUser->name }}</p>
                        <p class="text-xs text-slate-500">{{ $authUser->email ?? $authUser->phone }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    @foreach ($customerLinks as $link)
                        <a href="{{ $link['url'] }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-white">
                            <i class="{{ $link['icon'] }} w-4 text-center"></i>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('client.logout') }}" class="mt-2 border-t border-amber-100 pt-2">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-white">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        {{ __('client.nav.logout') }}
                    </button>
                </form>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-2 gap-2">
            @foreach ($languageOptions as $language)
                <a href="{{ route('client.locale.switch', ['locale' => $language['code'], 'redirect' => $localeRedirect]) }}"
                    class="flex items-center justify-center gap-2 rounded-xl border px-3 py-2 text-sm font-semibold {{ $currentLocale === $language['code'] ? 'border-primary-600 bg-primary-600 text-white' : 'border-slate-200 text-slate-600' }}">
                    <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}" class="h-4 w-4 rounded-full object-cover">
                    {{ strtoupper($language['code']) }}
                </a>
            @endforeach
        </div>

        @if ($hotline)
            <a href="tel:{{ $hotlineTel }}" class="mt-6 flex items-center justify-center gap-2 rounded-xl bg-emerald-50 py-3 text-sm font-bold text-emerald-700">
                <i class="fa-solid fa-phone-volume"></i>
                {{ $hotline }}
            </a>
        @endif

        @if ($email)
            <a href="{{ $emailHref }}" class="mt-2 flex items-center justify-center gap-2 rounded-xl bg-slate-100 py-3 text-sm font-semibold text-slate-700">
                <i class="fa-regular fa-envelope"></i>
                {{ $email }}
            </a>
        @endif
    </aside>
</header>
