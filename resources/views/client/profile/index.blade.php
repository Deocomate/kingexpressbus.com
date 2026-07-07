<x-client.layout :title="$title ?? __('client.profile.meta_title')" :description="$description ?? ''">
    @php
        $userName = $user->name ?? __('client.profile_page.default_user_name');
        $userEmail = $user->email ?? null;
        $userPhone = $user->phone ?? null;
        $preferredRoutes = ($preferredRoutes ?? collect())->values();
    @endphp

    <section class="relative border-b border-line bg-page text-ink">
        <div class="absolute inset-0 pointer-events-none bg-page"></div>

        <div
            class="ksb-section-hero relative container mx-auto flex flex-col items-center justify-between gap-8 px-4 md:flex-row">
            <div class="space-y-4 text-center md:text-left">
                <span
                    class="kx-badge uppercase tracking-wide">
                    <i class="fa-solid fa-crown mr-2"></i> {{ __('client.profile_page.hero.badge') }}
                </span>
                <h1 class="font-display text-4xl font-extrabold tracking-tight md:text-5xl">{{ __('client.profile_page.hero.greeting', ['name' => $userName]) }}</h1>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-6 text-muted text-base">
                    @if ($userEmail)
                        <span class="inline-flex items-center gap-2 transition-colors hover:text-ink"><i
                                class="fa-regular fa-envelope text-brand-600"></i>{{ $userEmail }}</span>
                    @endif
                    @if ($userPhone)
                        <span class="inline-flex items-center gap-2 transition-colors hover:text-ink"><i
                                class="fa-solid fa-phone text-pickup"></i>{{ $userPhone }}</span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('client.logout') }}" class="shrink-0">
                @csrf
                <button type="submit"
                    class="kx-btn-secondary group px-6 py-3 text-sm">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-power-off text-red-400 group-hover:text-red-300 transition-colors"></i>
                        <span>{{ __('client.nav.logout') }}</span>
                    </span>
                </button>
            </form>
        </div>
    </section>

    <section class="ksb-section min-h-screen bg-page">
        <div class="container mx-auto px-4 space-y-12">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div
                    class="kx-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-sm bg-primary-50 text-brand-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">{{ __('client.profile_page.stats.total_tickets') }}</span>
                    </div>
                    <p class="text-4xl font-semibold text-neutral-800 count-up"
                        data-target="{{ $stats['total_bookings'] ?? 0 }}">0</p>
                    <p class="text-sm text-neutral-500 mt-1">{{ __('client.profile_page.stats.total_tickets_desc') }}</p>
                </div>

                <!-- Card 2 -->
                <div
                    class="kx-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-sm bg-accent-50 text-accent-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">{{ __('client.profile_page.stats.upcoming') }}</span>
                    </div>
                    <p class="text-4xl font-semibold text-neutral-800 count-up"
                        data-target="{{ $stats['upcoming'] ?? 0 }}">0</p>
                    <p class="text-sm text-neutral-500 mt-1">{{ __('client.profile_page.stats.upcoming_desc') }}</p>
                </div>

                <!-- Card 3 -->
                <div
                    class="kx-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-sm bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">{{ __('client.profile_page.stats.completed') }}</span>
                    </div>
                    <p class="text-4xl font-semibold text-neutral-800 count-up"
                        data-target="{{ $stats['completed'] ?? 0 }}">0</p>
                    <p class="text-sm text-neutral-500 mt-1">{{ __('client.profile_page.stats.completed_desc') }}</p>
                </div>

                <!-- Card 4 -->
                <div
                    class="kx-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-sm bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">{{ __('client.profile_page.stats.spending') }}</span>
                    </div>
                    <p class="text-3xl font-semibold text-neutral-800 flex items-baseline">
                        <span class="count-up" data-target="{{ $stats['total_spent'] ?? 0 }}">0</span>
                        <span class="text-lg font-medium text-neutral-500 ml-1">đ</span>
                    </p>
                    <p class="text-sm text-neutral-500 mt-1">{{ __('client.profile_page.stats.spending_desc') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content Area -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Tabs Navigation -->
                    <div class="flex flex-nowrap overflow-x-auto gap-2 border-b border-neutral-200 pb-1" id="profileTabs">
                        <button
                            class="tab-btn active whitespace-nowrap px-6 py-3 text-sm font-semibold rounded-t-sm border-b-2 border-brand-600 text-brand-600 hover:bg-brand-50 transition-colors"
                            data-target="#upcoming">
                            {{ __('client.profile_page.tabs.upcoming') }} <span
                                class="ml-2 px-2 py-0.5 rounded-sm bg-brand-100 text-brand-600 text-xs">{{ $upcomingBookings->count() }}</span>
                        </button>
                        <button
                            class="tab-btn whitespace-nowrap px-6 py-3 text-sm font-semibold rounded-t-sm border-b-2 border-transparent text-neutral-500 hover:text-neutral-800 hover:bg-neutral-50 transition-colors"
                            data-target="#history">
                            {{ __('client.profile_page.tabs.history') }} <span
                                class="ml-2 px-2 py-0.5 rounded-sm bg-neutral-100 text-neutral-500 text-xs">{{ $bookingHistory->count() }}</span>
                        </button>
                    </div>

                    <!-- Tab Content: Upcoming -->
                    <div id="upcoming" class="tab-content space-y-4">
                        @forelse ($upcomingBookings as $booking)
                            <x-client.booking-card :booking="$booking" type="upcoming" />
                        @empty
                            <div class="kx-panel p-10 text-center border-dashed">
                                <div
                                    class="w-20 h-20 bg-primary-50 rounded-sm flex items-center justify-center mx-auto text-primary-500 mb-4">
                                    <i class="fa-solid fa-suitcase-rolling text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-neutral-800">{{ __('client.profile_page.empty_upcoming.title') }}</h3>
                                <p class="text-neutral-500 mt-2">{{ __('client.profile_page.empty_upcoming.description') }}</p>
                                <a href="{{ route('client.routes.index') }}"
                                    class="kx-btn-primary mt-6 px-6 py-3">
                                    <i class="fa-solid fa-magnifying-glass-location"></i>
                                    {{ __('client.profile_page.empty_upcoming.cta') }}
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Tab Content: History -->
                    <div id="history" class="tab-content hidden space-y-4">
                        @forelse ($bookingHistory as $booking)
                            <x-client.booking-card :booking="$booking" type="history" />
                        @empty
                            <div class="kx-panel p-10 text-center border-dashed">
                                <div
                                    class="w-20 h-20 bg-neutral-50 rounded-sm flex items-center justify-center mx-auto text-neutral-400 mb-4">
                                    <i class="fa-regular fa-calendar-xmark text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-neutral-800">{{ __('client.profile_page.empty_history.title') }}</h3>
                                <p class="text-neutral-500 mt-2">{{ __('client.profile_page.empty_history.description') }}</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($bookings->hasPages())
                        <div class="pt-4">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <aside class="space-y-6">
                    <!-- Preferred Routes -->
                    @if ($preferredRoutes->isNotEmpty())
                        <div class="kx-panel p-6">
                            <h2 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-heart text-red-500"></i> {{ __('client.profile_page.preferred_routes.title') }}
                            </h2>
                            <ul class="space-y-3">
                                @foreach ($preferredRoutes as $item)
                                    <li>
                                        <a href="{{ route('client.routes.show', ['slug' => $item['slug']]) }}"
                                            class="group flex items-center justify-between p-3 rounded-sm hover:bg-neutral-50 transition-colors border border-transparent hover:border-neutral-100">
                                            <span
                                                class="text-sm font-semibold text-neutral-700 group-hover:text-brand-600 transition-colors">
                                                {{ $item['name'] }}
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-1 text-xs font-semibold bg-brand-100 text-brand-600 px-2 py-1 rounded-sm">
                                                {{ $item['count'] }} <i class="fa-solid fa-check"></i>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Quick Actions / Support -->
                    <div
                        class="rounded-sm bg-contrast-900 p-6 text-white shadow-card">
                        <h2 class="text-lg font-semibold mb-4">{{ __('client.profile_page.support.title') }}</h2>
                        <ul class="space-y-3 text-sm text-primary-100 mb-6">
                            <li class="flex gap-2"><i class="fa-solid fa-check-circle mt-1"></i> <span>{{ __('client.profile_page.support.item_1') }}</span></li>
                            <li class="flex gap-2"><i class="fa-solid fa-check-circle mt-1"></i> <span>{{ __('client.profile_page.support.item_2') }}</span></li>
                            <li class="flex gap-2"><i class="fa-solid fa-check-circle mt-1"></i> <span>{{ __('client.profile_page.support.item_3') }}</span></li>
                        </ul>
                        <a href="{{ route('client.contact') }}"
                            class="block w-full text-center px-4 py-3 bg-white text-brand-600 rounded-sm font-semibold hover:bg-neutral-50 transition-colors duration-200">
                            {{ __('client.profile_page.support.cta') }}
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Tab Switching Logic
                const tabs = document.querySelectorAll('.tab-btn');
                const contents = document.querySelectorAll('.tab-content');

                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        // Remove active classes
                        tabs.forEach(t => {
                            t.classList.remove('border-brand-600', 'text-brand-600', 'active');
                            t.classList.add('border-transparent', 'text-neutral-500');
                        });
                        contents.forEach(c => c.classList.add('hidden'));

                        // Add active classes
                        tab.classList.remove('border-transparent', 'text-neutral-500');
                        tab.classList.add('border-brand-600', 'text-brand-600', 'active');

                        const target = document.querySelector(tab.dataset.target);
                        target.classList.remove('hidden');
                    });
                });

                // Count Up Animation
                const counters = document.querySelectorAll('.count-up');
                const speed = 200;

                const animateCount = () => {
                    counters.forEach(counter => {
                        const target = +counter.getAttribute('data-target');
                        const count = +counter.innerText.replace(/,/g, '');

                        const inc = target / speed;

                        if (count < target) {
                            counter.innerText = Math.ceil(count + inc).toLocaleString();
                            setTimeout(animateCount, 20);
                        } else {
                            counter.innerText = target.toLocaleString();
                        }
                    });
                }

                animateCount();
            });
        </script>
    @endpush
</x-client.layout>
