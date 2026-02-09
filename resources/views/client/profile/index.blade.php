<x-client.layout :title="$title ?? __('client.profile.meta_title')" :description="$description ?? ''">
    @php
        $userName = $user->name ?? 'Khách hàng';
        $userEmail = $user->email ?? null;
        $userPhone = $user->phone ?? null;
        $preferredRoutes = ($preferredRoutes ?? collect())->values();
    @endphp

    <section class="relative bg-neutral-800 text-white">
        <!-- Background Image -->
        <div class="absolute inset-0 opacity-20"
            style="background-image: url('/userfiles/files/kingexpressbus/cabin/1.jpg'); background-size: cover; background-position: center;">
        </div>
        <div class="absolute inset-0 bg-neutral-800/90"></div>

        <div
            class="relative container mx-auto px-4 py-16 lg:py-20 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-4 text-center md:text-left">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-accent-500 text-white uppercase tracking-wide">
                    <i class="fa-solid fa-crown mr-2"></i> Thành viên thân thiết
                </span>
                <h1 class="text-4xl md:text-5xl font-semibold tracking-tight">Xin chào, {{ $userName }}</h1>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-6 text-white/80 text-base">
                    @if ($userEmail)
                        <span class="inline-flex items-center gap-2 transition-colors hover:text-white"><i
                                class="fa-regular fa-envelope text-primary-400"></i>{{ $userEmail }}</span>
                    @endif
                    @if ($userPhone)
                        <span class="inline-flex items-center gap-2 transition-colors hover:text-white"><i
                                class="fa-solid fa-phone text-green-400"></i>{{ $userPhone }}</span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('client.logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit"
                    class="group relative px-6 py-3 rounded-md bg-white/10 border border-white/20 hover:bg-white/20 transition-colors duration-200 text-white font-semibold">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-power-off text-red-400 group-hover:text-red-300 transition-colors"></i>
                        <span>Đăng xuất</span>
                    </span>
                </button>
            </form>
        </div>
    </section>

    <section class="py-12 bg-neutral-50 min-h-screen">
        <div class="container mx-auto px-4 space-y-12">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div
                    class="bg-white rounded-lg p-6 shadow-soft hover:shadow-card transition-shadow duration-200 border border-neutral-200">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">Tổng vé</span>
                    </div>
                    <p class="text-4xl font-semibold text-neutral-800 count-up"
                        data-target="{{ $stats['total_bookings'] ?? 0 }}">0</p>
                    <p class="text-sm text-neutral-500 mt-1">Đã đặt thành công</p>
                </div>

                <!-- Card 2 -->
                <div
                    class="bg-white rounded-lg p-6 shadow-soft hover:shadow-card transition-shadow duration-200 border border-neutral-200">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-lg bg-accent-50 text-accent-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">Sắp đi</span>
                    </div>
                    <p class="text-4xl font-semibold text-neutral-800 count-up"
                        data-target="{{ $stats['upcoming'] ?? 0 }}">0</p>
                    <p class="text-sm text-neutral-500 mt-1">Chuyến sắp khởi hành</p>
                </div>

                <!-- Card 3 -->
                <div
                    class="bg-white rounded-lg p-6 shadow-soft hover:shadow-card transition-shadow duration-200 border border-neutral-200">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">Hoàn thành</span>
                    </div>
                    <p class="text-4xl font-semibold text-neutral-800 count-up"
                        data-target="{{ $stats['completed'] ?? 0 }}">0</p>
                    <p class="text-sm text-neutral-500 mt-1">Chuyến đi an toàn</p>
                </div>

                <!-- Card 4 -->
                <div
                    class="bg-white rounded-lg p-6 shadow-soft hover:shadow-card transition-shadow duration-200 border border-neutral-200">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <span class="text-xs font-semibold text-neutral-400 uppercase">Chi tiêu</span>
                    </div>
                    <p class="text-3xl font-semibold text-neutral-800 flex items-baseline">
                        <span class="count-up" data-target="{{ $stats['total_spent'] ?? 0 }}">0</span>
                        <span class="text-lg font-medium text-neutral-500 ml-1">đ</span>
                    </p>
                    <p class="text-sm text-neutral-500 mt-1">Tổng tiền vé</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content Area -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Tabs Navigation -->
                    <div class="flex flex-nowrap overflow-x-auto gap-2 border-b border-neutral-200 pb-1" id="profileTabs">
                        <button
                            class="tab-btn active whitespace-nowrap px-6 py-3 text-sm font-semibold rounded-t-lg border-b-2 border-primary-600 text-primary-600 hover:bg-primary-50 transition-colors"
                            data-target="#upcoming">
                            Sắp khởi hành <span
                                class="ml-2 px-2 py-0.5 rounded-md bg-primary-100 text-primary-600 text-xs">{{ $upcomingBookings->count() }}</span>
                        </button>
                        <button
                            class="tab-btn whitespace-nowrap px-6 py-3 text-sm font-semibold rounded-t-lg border-b-2 border-transparent text-neutral-500 hover:text-neutral-800 hover:bg-neutral-50 transition-colors"
                            data-target="#history">
                            Lịch sử đặt vé <span
                                class="ml-2 px-2 py-0.5 rounded-md bg-neutral-100 text-neutral-500 text-xs">{{ $bookingHistory->count() }}</span>
                        </button>
                    </div>

                    <!-- Tab Content: Upcoming -->
                    <div id="upcoming" class="tab-content space-y-4">
                        @forelse ($upcomingBookings as $booking)
                            <x-client.booking-card :booking="$booking" type="upcoming" />
                        @empty
                            <div class="bg-white rounded-lg p-10 text-center border border-dashed border-neutral-300">
                                <div
                                    class="w-20 h-20 bg-primary-50 rounded-lg flex items-center justify-center mx-auto text-primary-500 mb-4">
                                    <i class="fa-solid fa-suitcase-rolling text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-neutral-800">Chưa có chuyến đi nào</h3>
                                <p class="text-neutral-500 mt-2">Hãy lên kế hoạch cho chuyến đi tiếp theo của bạn ngay hôm
                                    nay.</p>
                                <a href="{{ route('client.routes.search') }}"
                                    class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-semibold transition-colors duration-200">
                                    <i class="fa-solid fa-magnifying-glass-location"></i>
                                    Tìm chuyến xe
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Tab Content: History -->
                    <div id="history" class="tab-content hidden space-y-4">
                        @forelse ($bookingHistory as $booking)
                            <x-client.booking-card :booking="$booking" type="history" />
                        @empty
                            <div class="bg-white rounded-lg p-10 text-center border border-dashed border-neutral-300">
                                <div
                                    class="w-20 h-20 bg-neutral-50 rounded-lg flex items-center justify-center mx-auto text-neutral-400 mb-4">
                                    <i class="fa-regular fa-calendar-xmark text-3xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-neutral-800">Lịch sử trống</h3>
                                <p class="text-neutral-500 mt-2">Bạn chưa thực hiện chuyến đi nào tại King Express Bus.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="space-y-6">
                    <!-- Preferred Routes -->
                    @if ($preferredRoutes->isNotEmpty())
                        <div class="bg-white border border-neutral-200 rounded-lg shadow-soft p-6">
                            <h2 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-heart text-red-500"></i> Tuyến đi yêu thích
                            </h2>
                            <ul class="space-y-3">
                                @foreach ($preferredRoutes as $item)
                                    <li>
                                        <a href="{{ route('client.routes.show', ['slug' => $item['slug']]) }}"
                                            class="group flex items-center justify-between p-3 rounded-md hover:bg-neutral-50 transition-colors border border-transparent hover:border-neutral-100">
                                            <span
                                                class="text-sm font-semibold text-neutral-700 group-hover:text-primary-600 transition-colors">
                                                {{ $item['name'] }}
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-1 text-xs font-semibold bg-primary-100 text-primary-600 px-2 py-1 rounded-md">
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
                        class="bg-primary-600 rounded-lg shadow-card p-6 text-white">
                        <h2 class="text-lg font-semibold mb-4">Bạn cần hỗ trợ?</h2>
                        <ul class="space-y-3 text-sm text-primary-100 mb-6">
                            <li class="flex gap-2"><i class="fa-solid fa-check-circle mt-1"></i> <span>Thay đổi lịch
                                    trình dễ dàng</span></li>
                            <li class="flex gap-2"><i class="fa-solid fa-check-circle mt-1"></i> <span>Hỗ trợ hủy vé
                                    online</span></li>
                            <li class="flex gap-2"><i class="fa-solid fa-check-circle mt-1"></i> <span>Tư vấn qua tổng
                                    đài 24/7</span></li>
                        </ul>
                        <a href="{{ route('client.contact') }}"
                            class="block w-full text-center px-4 py-3 bg-white text-primary-600 rounded-md font-semibold hover:bg-neutral-50 transition-colors duration-200">
                            Liên hệ ngay
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
                            t.classList.remove('border-primary-600', 'text-primary-600', 'active');
                            t.classList.add('border-transparent', 'text-neutral-500');
                        });
                        contents.forEach(c => c.classList.add('hidden'));

                        // Add active classes
                        tab.classList.remove('border-transparent', 'text-neutral-500');
                        tab.classList.add('border-primary-600', 'text-primary-600', 'active');

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
