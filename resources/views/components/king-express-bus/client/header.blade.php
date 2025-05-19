{{-- Sử dụng Alpine.js cho mobile menu toggle và user dropdown --}}
<header x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="bg-yellow-600 shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            {{-- Logo/Brand Name --}}
            <div class="flex-shrink-0"> {{-- Thêm flex-shrink-0 --}}
                <a href="{{ route('homepage') }}"
                   class="text-white font-bold text-2xl flex items-center"> {{-- Thêm flex items-center --}}
                    @if(!empty($webInfoGlobal->logo))
                        <img src="{{ $webInfoGlobal->logo }}" alt="{{ $webInfoGlobal->title ?? config('app.name') }}"
                             class="h-8 md:h-10 mr-2"> {{-- Bỏ inline --}}
                    @else
                        {{ $webInfoGlobal->title ?? config('app.name', 'KingExpressBus') }}
                    @endif
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <nav
                class="hidden md:flex items-center space-x-6 flex-grow justify-center"> {{-- Thêm flex-grow justify-center --}}
                @if(isset($clientMenuTreeGlobal) && $clientMenuTreeGlobal->isNotEmpty())
                    @foreach($clientMenuTreeGlobal as $item)
                        @php
                            $hasChildren = !empty($item->children) && $item->children->isNotEmpty();
                            $url = $item->url ? (Route::has($item->url) ? route($item->url) : url($item->url)) : '#';
                            // Kiểm tra active (ví dụ đơn giản)
                            $isActive = request()->fullUrlIs($url) || ($item->url && Route::has($item->url) && request()->routeIs($item->url.'*'));
                        @endphp

                        @if($hasChildren)
                            {{-- Desktop Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @mouseover="open = true" @mouseleave="open = false"
                                        :class="{ 'text-yellow-100': open, 'text-white hover:text-yellow-100': !open }"
                                        class="font-medium flex items-center transition-colors duration-150 {{ $isActive ? 'text-yellow-100 font-semibold' : '' }}">
                                    <span>{{ $item->name }}</span>
                                    <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                         :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     @mouseover="open = true" @mouseleave="open = false"
                                     class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                     style="display: none;">
                                    @foreach($item->children as $child)
                                        @php
                                            $childUrl = $child->url ? (Route::has($child->url) ? route($child->url) : url($child->url)) : '#';
                                            $isChildActive = request()->fullUrlIs($childUrl) || ($child->url && Route::has($child->url) && request()->routeIs($child->url.'*'));
                                        @endphp
                                        <a href="{{ $childUrl }}"
                                           class="block px-4 py-2 text-sm {{ $isChildActive ? 'bg-yellow-100 text-yellow-800 font-semibold' : 'text-gray-700 hover:bg-yellow-50 hover:text-yellow-700' }}">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Desktop Simple Link --}}
                            <a href="{{ $url }}"
                               class="{{ $isActive ? 'text-yellow-100 font-semibold' : 'text-white hover:text-yellow-100' }} font-medium transition-colors duration-150">
                                {{ $item->name }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('homepage') }}" class="text-white hover:text-yellow-100 font-medium">Trang chủ</a>
                @endif
            </nav>

            {{-- Right Side Actions (Auth Status Dependent) --}}
            <div class="flex items-center space-x-4 flex-shrink-0"> {{-- Thêm flex-shrink-0 --}}

                {{-- Hiển thị nếu KHÁCH (chưa đăng nhập) --}}
                @if(!session()->has('customer_id'))
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ route('client.login_page') }}"
                           class="text-white hover:text-yellow-100 text-sm font-medium transition-colors duration-150">
                            Đăng nhập
                        </a>
                        <a href="{{ route('client.register_page') }}"
                           class="bg-white text-yellow-600 hover:bg-yellow-100 py-2 px-4 rounded-lg text-sm font-medium shadow transition duration-150">
                            Đăng ký
                        </a>
                    </div>
                @endif

                {{-- Hiển thị nếu ĐÃ ĐĂNG NHẬP --}}
                @if(session()->has('customer_id'))
                    <div class="hidden md:flex items-center relative" x-data="{ userDropdownOpen: false }">
                        {{-- Nút hiển thị tên và mở dropdown --}}
                        <button @click="userDropdownOpen = !userDropdownOpen" @click.away="userDropdownOpen = false"
                                class="flex items-center text-white hover:text-yellow-100 text-sm font-medium focus:outline-none transition-colors duration-150">
                            <span>Xin chào, {{ Str::limit(session('customer_name', 'Bạn'), 15) }}</span> {{-- Giới hạn độ dài tên --}}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        {{-- User Dropdown Panel --}}
                        <div x-show="userDropdownOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 top-full w-48 bg-white rounded-md shadow-lg py-1 z-50"
                             style="display: none;">
                            <a href="{{ route('client.user_information_page') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700">
                                Thông tin tài khoản
                            </a>
                            <a href="{{ route('client.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               {{-- Thêm form logout ẩn --}}
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700">
                                Đăng xuất
                            </a>
                            {{-- Form ẩn cho logout (nên dùng POST hoặc GET có CSRF) --}}
                            <form id="logout-form" action="{{ route('client.logout') }}" method="GET"
                                  style="display: none;">
                                {{-- @csrf --}} {{-- CSRF không cần thiết cho GET logout đơn giản, nhưng POST sẽ an toàn hơn --}}
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Mobile Menu Toggle Button --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white focus:outline-none">
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation Menu --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="md:hidden mt-3 pb-3 border-t border-yellow-500"
             style="display: none;"
        >
            <nav class="flex flex-col space-y-1 pt-2">
                {{-- Render menu đa cấp cho mobile --}}
                @if(isset($clientMenuTreeGlobal) && $clientMenuTreeGlobal->isNotEmpty())
                    @foreach($clientMenuTreeGlobal as $item)
                        <x-king-express-bus.client.menu-item :item="$item"/>
                    @endforeach
                @else
                    <a href="{{ route('homepage') }}"
                       class="block px-4 py-2 text-white hover:bg-yellow-700 hover:text-yellow-100 font-medium">Trang
                        chủ</a>
                @endif

                {{-- Hiển thị nút Auth phù hợp trên Mobile --}}
                <div class="border-t border-yellow-500 pt-4 mt-4 space-y-2 px-4">
                    @if(!session()->has('customer_id'))
                        {{-- Chưa đăng nhập --}}
                        <a href="{{ route('client.login_page') }}"
                           class="block text-center w-full bg-white text-yellow-600 hover:bg-yellow-100 py-2 px-4 rounded-lg font-medium">
                            Đăng nhập </a>
                        <a href="{{ route('client.register_page') }}"
                           class="block text-center w-full bg-yellow-700 text-white hover:bg-yellow-800 py-2 px-4 rounded-lg font-medium">
                            Đăng ký </a>
                    @else
                        {{-- Đã đăng nhập --}}
                        <a href="{{ route('client.user_information_page') }}"
                           class="block px-4 py-2 text-white hover:bg-yellow-700 hover:text-yellow-100 font-medium">
                            Thông tin tài khoản </a>
                        <a href="{{ route('client.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('mobile-logout-form').submit();"
                           class="block px-4 py-2 text-white hover:bg-yellow-700 hover:text-yellow-100 font-medium">
                            Đăng xuất </a>
                        <form id="mobile-logout-form" action="{{ route('client.logout') }}" method="GET"
                              style="display: none;"></form>
                    @endif
                </div>
            </nav>
        </div>
    </div>
</header>
