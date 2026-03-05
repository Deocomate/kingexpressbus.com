@php
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = data_get($webProfile, 'logo_url');
    $hotline = data_get($webProfile, 'hotline');
    $hotlineTel = $hotline ? preg_replace('/[^\d+]/', '', $hotline) : '';
    $authUser = $authUser ?? null;
    $isCustomer = $authUser && ($authUser->role ?? null) === 'customer';
    $customerLinks = $customerLinks ?? [];
    $currentLocale = app()->getLocale();
    $languageOptions = [
        ['code' => 'vi', 'label' => 'Tiếng Việt', 'flag' => asset('/client/icons/vn-flag.svg')],
        ['code' => 'en', 'label' => 'English', 'flag' => asset('/client/icons/en-flag.svg')],
    ];
    $currentLanguage = collect($languageOptions)->firstWhere('code', $currentLocale);
@endphp

<header class="bg-white shadow-soft sticky top-0 z-50 border-b border-neutral-200">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            {{-- Brand Logo --}}
            <a href="{{ route('client.home') }}" class="flex-shrink-0 flex items-center gap-2"
                aria-label="{{ __('client.nav.home_aria') }}">
                @if ($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10 w-auto">
                @else
                    <span class="text-2xl font-semibold text-primary-600">{{ $brandTitle }}</span>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center gap-1">
                @foreach ($mainMenu ?? [] as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $hasChildren = !empty($item->children);
                    @endphp

                    @if (!$hasChildren)
                        <a href="{{ url($item->url) }}"
                            class="font-medium transition-colors duration-200 px-4 py-2 rounded-md whitespace-nowrap {{ $isActive ? 'bg-primary-50 text-primary-600' : 'text-neutral-700 hover:bg-neutral-100 hover:text-primary-600' }}">
                            {{ $item->name }}
                        </a>
                    @else
                        <div class="relative group">
                            <a href="{{ url($item->url) }}"
                                class="flex items-center gap-1.5 font-medium transition-colors duration-200 px-4 py-2 rounded-md whitespace-nowrap {{ $isActive || ($item->isParentOfActive ?? false) ? 'bg-primary-50 text-primary-600' : 'text-neutral-700 hover:bg-neutral-100 hover:text-primary-600' }}">
                                <span>{{ $item->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                            </a>
                            <div
                                class="absolute left-0 mt-0 pt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-200 z-50">
                                <div class="bg-white border border-neutral-200 rounded-lg shadow-dropdown py-2 max-h-80 overflow-y-auto">
                                    @foreach ($item->children as $child)
                                        <a href="{{ url($child->url) }}"
                                            class="block px-4 py-2.5 text-sm rounded mx-1 {{ ($child->isActive ?? false) ? 'bg-primary-50 text-primary-600 font-semibold' : 'text-neutral-600 hover:bg-neutral-100 hover:text-primary-600' }}">
                                            <span class="flex items-center gap-2">
                                                <i class="fa-solid fa-route text-xs text-primary-400"></i>
                                                {{ $child->name }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            {{-- Desktop Actions --}}
            <div class="hidden lg:flex items-center gap-3">
                {{-- Language Switcher --}}
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-neutral-100 transition-colors duration-200">
                        @if($currentLanguage && $currentLanguage['flag'])
                            <img src="{{ $currentLanguage['flag'] }}" alt="{{ $currentLanguage['label'] }}"
                                class="w-5 h-5 rounded-full object-cover">
                        @endif
                        <span class="font-medium text-sm text-neutral-700 uppercase">{{ $currentLanguage['code'] }}</span>
                        <i class="fa-solid fa-chevron-down text-xs text-neutral-400"></i>
                    </button>
                    <div
                        class="absolute right-0 mt-0 pt-2 w-40 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-200">
                        <div class="bg-white border border-neutral-200 rounded-lg shadow-dropdown py-1.5">
                            @foreach ($languageOptions as $language)
                                <a href="{{ route('client.locale.switch', ['locale' => $language['code']]) }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-neutral-100 transition-colors duration-200 {{ $currentLocale === $language['code'] ? 'font-semibold text-primary-600' : 'text-neutral-700' }}">
                                    <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}"
                                        class="w-5 h-5 rounded-full object-cover">
                                    <span>{{ $language['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Hotline --}}
                @if ($hotline)
                    <a href="tel:{{ $hotlineTel }}"
                        class="flex items-center gap-2 text-green-700 font-semibold px-3 py-2 rounded-md hover:bg-green-50 transition-colors duration-200">
                        <i class="fas fa-phone-alt text-sm"></i>
                        <span class="text-sm">{{ $hotline }}</span>
                    </a>
                @endif

                {{-- Auth Section --}}
                @if ($isCustomer)
                    <div class="relative group">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-neutral-100 transition-colors duration-200">
                            <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-sm text-neutral-800 max-w-[100px] truncate">{{ $authUser->name }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-neutral-400"></i>
                        </button>
                        <div
                            class="absolute right-0 mt-0 pt-2 w-56 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-200">
                          <div class="bg-white border border-neutral-200 rounded-lg shadow-dropdown py-2">
                            <div class="px-4 py-3 border-b border-neutral-100">
                                <p class="font-semibold text-neutral-800">{{ $authUser->name }}</p>
                                <p class="text-sm text-neutral-500 truncate">{{ $authUser->email ?? $authUser->phone }}</p>
                            </div>
                            <div class="py-2">
                                @foreach ($customerLinks as $link)
                                    <a href="{{ $link['url'] }}"
                                        class="flex items-center gap-3 px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 hover:text-primary-600 transition-colors duration-200">
                                        <i class="{{ $link['icon'] }} w-4 text-center text-neutral-400"></i>
                                        <span>{{ $link['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                            <form method="POST" action="{{ route('client.logout') }}"
                                class="pt-2 border-t border-neutral-100">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                                    <span>{{ __('client.nav.logout') }}</span>
                                </button>
                            </form>
                          </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('client.login') }}"
                            class="text-sm font-medium text-neutral-700 px-4 py-2 rounded-md hover:bg-neutral-100 transition-colors duration-200">
                            {{ __('client.nav.login') }}
                        </a>
                        <a href="{{ route('client.register') }}"
                            class="text-sm font-semibold bg-accent-500 text-white px-4 py-2 rounded-md hover:bg-accent-600 transition-colors duration-200">
                            {{ __('client.nav.register') }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Mobile Menu Button --}}
            <button id="mobile-menu-button"
                class="lg:hidden text-2xl text-neutral-700 hover:text-primary-600 transition-colors duration-200"
                aria-label="{{ __('client.nav.open_menu') }}">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Panel --}}
    <div id="mobile-menu"
        class="lg:hidden fixed top-0 left-0 w-80 h-screen bg-white shadow-elevated z-[1000] transform -translate-x-full"
        aria-hidden="true">
        <div class="p-5 flex flex-col h-full">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('client.home') }}" class="flex items-center gap-2"
                    aria-label="{{ __('client.nav.home_aria') }}">
                    @if ($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10">
                    @else
                        <span class="text-xl font-semibold text-primary-600">{{ $brandTitle }}</span>
                    @endif
                </a>
                <button id="close-mobile-menu" class="text-3xl text-neutral-400 hover:text-neutral-600">&times;</button>
            </div>

            {{-- Auth for Logged Out --}}
            @if (!$isCustomer)
                <div class="flex items-center gap-3 mb-6">
                    <a href="{{ route('client.login') }}"
                        class="flex-1 text-center border border-neutral-200 rounded-md py-2.5 font-medium text-neutral-700 hover:bg-neutral-50 transition-colors duration-200">
                        {{ __('client.nav.login') }}
                    </a>
                    <a href="{{ route('client.register') }}"
                        class="flex-1 text-center bg-accent-500 text-white font-semibold rounded-md py-2.5 hover:bg-accent-600 transition-colors duration-200">
                        {{ __('client.nav.register') }}
                    </a>
                </div>
            @endif

            {{-- Navigation --}}
            <nav class="flex-grow overflow-y-auto space-y-1">
                @foreach ($mainMenu ?? [] as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $hasChildren = !empty($item->children);
                        $icon = match ($item->id ?? '') {
                            'static_home' => 'fa-solid fa-home',
                            'static_about' => 'fa-solid fa-info-circle',
                            'static_routes' => 'fa-solid fa-map-signs',
                            'static_hot_routes' => 'fa-solid fa-fire',
                            'static_contact' => 'fa-solid fa-phone',
                            default => 'fa-solid fa-link',
                        };
                    @endphp

                    @if (!$hasChildren)
                        <a href="{{ url($item->url) }}"
                            class="flex items-center gap-3 font-medium py-3 px-4 rounded-md transition-colors duration-200 {{ $isActive ? 'bg-primary-50 text-primary-600' : 'text-neutral-700 hover:bg-neutral-100' }}">
                            <i class="{{ $icon }} w-5 text-center"></i>
                            {{ $item->name }}
                        </a>
                    @else
                        <div x-data="{ open: false }">
                            <div class="flex items-center">
                                <a href="{{ url($item->url) }}"
                                    class="flex-1 flex items-center gap-3 font-medium py-3 px-4 rounded-md transition-colors duration-200 {{ $isActive || ($item->isParentOfActive ?? false) ? 'bg-primary-50 text-primary-600' : 'text-neutral-700 hover:bg-neutral-100' }}">
                                    <i class="{{ $icon }} w-5 text-center"></i>
                                    {{ $item->name }}
                                </a>
                                <button @click="open = !open" class="p-3 text-neutral-500 hover:text-primary-600">
                                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                                        :class="{ 'rotate-180': open }"></i>
                                </button>
                            </div>
                            <div x-show="open" x-collapse class="pl-8 mt-1 space-y-1 border-l-2 border-primary-100 ml-6">
                                @foreach ($item->children as $child)
                                    <a href="{{ url($child->url) }}"
                                        class="flex items-center gap-2 font-medium py-2 px-3 rounded-md transition-colors duration-200 {{ ($child->isActive ?? false) ? 'text-primary-600 font-semibold bg-primary-50' : 'text-neutral-600 hover:text-primary-600 hover:bg-neutral-50' }}">
                                        <i class="fa-solid fa-route text-xs text-primary-400"></i>
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            {{-- Footer section of mobile menu --}}
            <div class="mt-auto pt-4 border-t border-neutral-200 space-y-4">
                {{-- Auth for Logged In --}}
                @if ($isCustomer)
                    <div class="border border-neutral-200 rounded-lg p-4 bg-neutral-50 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-neutral-800 truncate">{{ $authUser->name }}</p>
                                <p class="text-sm text-neutral-500 truncate">{{ $authUser->email ?? $authUser->phone }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-2 pt-3 border-t border-neutral-200">
                            @foreach ($customerLinks as $link)
                                <a href="{{ $link['url'] }}"
                                    class="flex items-center gap-3 text-sm text-primary-600 hover:text-primary-700 transition-colors duration-200">
                                    <i class="{{ $link['icon'] }} w-4 text-center"></i>
                                    <span>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                        <form method="POST" action="{{ route('client.logout') }}" class="pt-3 border-t border-neutral-200">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-start gap-3 text-sm text-red-600 hover:text-red-700 transition-colors duration-200">
                                <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                                <span>{{ __('client.nav.logout') }}</span>
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Language Switcher --}}
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($languageOptions as $language)
                        <a href="{{ route('client.locale.switch', ['locale' => $language['code']]) }}"
                            class="flex items-center justify-center gap-2 py-2.5 rounded-md border text-sm font-medium transition-colors duration-200 {{ $currentLocale === $language['code'] ? 'bg-primary-600 text-white border-primary-600' : 'border-neutral-200 text-neutral-600 hover:bg-neutral-100' }}">
                            <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}"
                                class="w-5 h-5 rounded-full object-cover">
                            <span>{{ $language['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- Hotline --}}
                @if ($hotline)
                    <a href="tel:{{ $hotlineTel }}"
                        class="flex items-center gap-3 bg-green-50 text-green-700 font-semibold px-4 py-3 rounded-md border border-green-200 transition-colors duration-200 hover:bg-green-100">
                        <i class="fas fa-phone-alt"></i>
                        <span class="flex flex-col text-left leading-tight">
                            <span class="text-xs uppercase tracking-wide text-green-600">{{ __('client.nav.hotline') }}</span>
                            <span>{{ $hotline }}</span>
                        </span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div id="mobile-menu-overlay" class="hidden lg:hidden fixed inset-0 bg-black/40 z-[999]"></div>
</header>
