{{--
    Footer Component View
    Sử dụng Tailwind CSS và dữ liệu từ $webInfoGlobal
--}}
<footer class="bg-yellow-700 text-white"> {{-- Màu nền vàng đậm hơn header --}}
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Cột 1: Thông tin công ty --}}
            <div class="mb-6 md:mb-0">
                <h4 class="text-xl font-bold mb-4">
                    {{-- Logo hoặc Tên công ty --}}
                    @if(!empty($webInfoGlobal->logo))
                        <img src="{{ $webInfoGlobal->logo }}" alt="{{ $webInfoGlobal->title ?? config('app.name') }}"
                             class="h-10 mb-2"> {{-- Điều chỉnh chiều cao logo --}}
                    @else
                        {{ $webInfoGlobal->title ?? config('app.name', 'KingExpressBus') }}
                    @endif
                </h4>
                <p class="text-yellow-100 text-sm leading-relaxed">
                    {{ $webInfoGlobal->description ?? 'Hệ thống đặt vé xe khách trực tuyến hàng đầu Việt Nam.' }}
                </p>
                {{-- Thêm các icon mạng xã hội nếu có --}}
                <div class="flex space-x-4 mt-4">
                    @if(!empty($webInfoGlobal->facebook))
                        <a href="{{ $webInfoGlobal->facebook }}" target="_blank" rel="noopener noreferrer"
                           class="text-yellow-100 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span class="sr-only">Facebook</span> {{-- Screen reader text --}}
                        </a>
                    @endif
                    @if(!empty($webInfoGlobal->zalo))
                        {{-- Thêm icon Zalo nếu bạn có SVG hoặc sử dụng ảnh --}}
                        <a href="{{ $webInfoGlobal->zalo }}" target="_blank" rel="noopener noreferrer"
                           class="text-yellow-100 hover:text-white transition-colors">
                            {{-- Thay thế bằng SVG Zalo nếu có --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.438 13.375h-3.063v3.062a.813.813 0 11-1.625 0v-3.062H8.688a.813.813 0 110-1.625h3.063V8.688a.813.813 0 111.625 0v3.062h3.063a.813.813 0 110 1.625zM12 1.063C5.938 1.063 1.063 5.937 1.063 12c0 6.062 4.875 10.937 10.937 10.937 6.063 0 10.938-4.875 10.938-10.937C22.938 5.938 18.063 1.063 12 1.063zm0 20.25c-5.125 0-9.313-4.188-9.313-9.313S6.875 2.687 12 2.687c5.125 0 9.313 4.188 9.313 9.313S17.125 21.313 12 21.313z"/>
                            </svg>
                            <span class="sr-only">Zalo</span>
                        </a>
                    @endif
                    {{-- Thêm các mạng xã hội khác nếu cần --}}
                </div>
            </div>

            {{-- Cột 2: Liên kết nhanh (Ví dụ) --}}
            <div class="mb-6 md:mb-0">
                <h5 class="text-lg font-semibold mb-4 uppercase">Liên kết</h5>
                <ul class="space-y-2">
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Về chúng tôi</a>
                    </li>
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Lịch trình</a>
                    </li>
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Khuyến mãi</a>
                    </li>
                    {{-- Lấy link Chính sách từ $webInfoGlobal nếu có --}}
                    @if(!empty($webInfoGlobal->policy) && !empty($webInfoGlobal->policy_link))
                        {{-- Giả sử có trường policy_link --}}
                        <li><a href="{{ $webInfoGlobal->policy_link }}"
                               class="text-yellow-100 hover:text-white text-sm transition-colors">Chính sách</a></li>
                    @else
                        <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Chính
                                sách</a></li>
                    @endif
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Liên hệ</a></li>
                </ul>
            </div>

            {{-- Cột 3: Hỗ trợ khách hàng --}}
            <div class="mb-6 md:mb-0">
                <h5 class="text-lg font-semibold mb-4 uppercase">Hỗ trợ</h5>
                <ul class="space-y-2">
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Câu hỏi thường
                            gặp</a></li>
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Hướng dẫn đặt
                            vé</a></li>
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Phương thức thanh
                            toán</a></li>
                    <li><a href="#" class="text-yellow-100 hover:text-white text-sm transition-colors">Quy định đổi trả
                            vé</a></li>
                </ul>
            </div>

            {{-- Cột 4: Thông tin liên hệ --}}
            <div>
                <h5 class="text-lg font-semibold mb-4 uppercase">Liên hệ</h5>
                <ul class="space-y-3 text-sm">
                    @if(!empty($webInfoGlobal->address))
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-2 mt-0.5 text-yellow-300 flex-shrink-0" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-yellow-100">{{ $webInfoGlobal->address }}</span>
                        </li>
                    @endif
                    @if(!empty($webInfoGlobal->hotline))
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-300 flex-shrink-0" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <a href="tel:{{ $webInfoGlobal->hotline }}"
                               class="text-yellow-100 hover:text-white transition-colors">Hotline: {{ $webInfoGlobal->hotline }}</a>
                        </li>
                    @endif
                    @if(!empty($webInfoGlobal->email))
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-300 flex-shrink-0" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <a href="mailto:{{ $webInfoGlobal->email }}"
                               class="text-yellow-100 hover:text-white transition-colors">{{ $webInfoGlobal->email }}</a>
                        </li>
                    @endif
                </ul>
            </div>

        </div>

        {{-- Phần Copyright --}}
        <div class="mt-8 pt-6 border-t border-yellow-600 text-center text-sm text-yellow-200">
            &copy; {{ date('Y') }} {{ $webInfoGlobal->title ?? config('app.name', 'KingExpressBus') }}. All rights
            reserved.
        </div>
    </div>
</footer>
