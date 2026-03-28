<x-client.layout :title="$title ?? __('client.page.meta_title')" :description="$description ?? ''" body-class="bg-[#F8FAFC] text-slate-800">
    @php
        $updatedAt = $page->updated_at ?? null;
        $displayUpdatedAt = $updatedAt ? \Carbon\Carbon::parse($updatedAt)->format('d/m/Y H:i') : null;
    @endphp

    @push('styles')
        <style>
            @keyframes page-hero-drift {
                0% {
                    background-position: center top;
                }

                100% {
                    background-position: center bottom;
                }
            }

            .page-hero-bg {
                background-image:
                    linear-gradient(112deg, rgba(15, 23, 42, 0.78), rgba(255, 155, 0, 0.52)),
                    url('/client/images/city_imgs/da-nang.jpg');
                background-size: cover;
                background-position: center;
                animation: page-hero-drift 18s ease-in-out infinite alternate;
            }

            @media (prefers-reduced-motion: reduce) {
                .page-hero-bg {
                    animation: none;
                }
            }

            .page-content-prose :where(h2, h3, h4) {
                color: #0f172a;
                font-weight: 800;
                scroll-margin-top: 96px;
            }

            .page-content-prose a {
                color: #FF9B00;
                font-weight: 700;
                text-decoration: none;
            }

            .page-content-prose a:hover {
                color: #e68a00;
            }
        </style>
    @endpush

    {{-- HEADER --}}
    {{-- Header được render trong x-client.layout --}}

    {{-- HERO/SEARCH --}}
    <section class="page-hero-bg relative overflow-visible px-4 pb-14 pt-20 md:pb-20 md:pt-24">
        <div class="container mx-auto max-w-7xl">
            <div class="max-w-3xl">
                <span
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-white">
                    <i class="fa-solid fa-sparkles" aria-hidden="true"></i>
                    Thông tin hành trình King Express Bus
                </span>
                <h1 class="text-3xl font-extrabold leading-tight text-white sm:text-4xl lg:text-5xl">
                    {{ $page->title ?? 'Nội dung hữu ích cho chuyến đi của bạn' }}
                </h1>
                <p class="mt-4 text-sm text-slate-100/95 sm:text-base lg:text-lg">
                    Tìm nhanh tuyến xe, cập nhật thông tin mới nhất và chuẩn bị hành trình thuận tiện với trải nghiệm đặt vé trực quan, hiện đại.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @if ($displayUpdatedAt)
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            Cập nhật: {{ $displayUpdatedAt }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                        <i class="fa-solid fa-shield-heart" aria-hidden="true"></i>
                        Thông tin minh bạch, dễ theo dõi
                    </span>
                </div>
            </div>

            <div class="relative z-90 mt-8">
                <x-client.search-bar submit-label="Tìm chuyến ngay" />
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">
            <article class="lg:col-span-8 space-y-5 rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-100 pb-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-primary-600">Nội dung chi tiết</p>
                        <h2 class="mt-1 text-xl font-extrabold text-slate-800 md:text-2xl">{{ $page->title ?? 'Thông tin chi tiết' }}</h2>
                    </div>
                    <a href="{{ route('client.routes.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-primary-600/25 bg-primary-50 px-4 py-2 text-sm font-bold text-primary-700 transition-all duration-300 hover:-translate-y-1 hover:bg-primary-600 hover:text-white active:scale-95">
                        Đặt vé ngay
                        <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="page-content-prose prose prose-slate max-w-none prose-headings:font-extrabold prose-p:text-slate-600 prose-li:text-slate-600 prose-img:rounded-2xl prose-img:shadow-soft">
                    {!! $page->content !!}
                </div>
            </article>

            <aside class="lg:col-span-4 space-y-5">
                <div
                    class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                        <i class="fa-solid fa-headset text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-800">Hỗ trợ đặt vé 24/7</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Cần tư vấn thêm về điểm đón, điểm trả hay thay đổi lịch trình? Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ bạn.
                    </p>
                    <a href="{{ route('client.contact') }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-soft transition-all duration-300 hover:bg-primary-700 active:scale-95">
                        Liên hệ hỗ trợ
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft">
                    <h3 class="text-base font-extrabold text-slate-800">Điểm đến nổi bật</h3>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('client.routes.index') }}"
                            class="group flex items-center gap-3 rounded-xl border border-slate-100 p-2 transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:shadow-soft">
                            <img src="/client/images/city_imgs/sapa.jpg" alt="Sa Pa"
                                class="h-14 w-20 rounded-lg object-cover">
                            <div>
                                <p class="text-sm font-bold text-slate-700">Sa Pa</p>
                                <p class="text-xs text-slate-500">Không khí mát lạnh quanh năm</p>
                            </div>
                        </a>
                        <a href="{{ route('client.routes.index') }}"
                            class="group flex items-center gap-3 rounded-xl border border-slate-100 p-2 transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:shadow-soft">
                            <img src="/client/images/city_imgs/ha-noi.jpg" alt="Hà Nội"
                                class="h-14 w-20 rounded-lg object-cover">
                            <div>
                                <p class="text-sm font-bold text-slate-700">Hà Nội</p>
                                <p class="text-xs text-slate-500">Ẩm thực và văn hóa đặc sắc</p>
                            </div>
                        </a>
                        <a href="{{ route('client.routes.index') }}"
                            class="group flex items-center gap-3 rounded-xl border border-slate-100 p-2 transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:shadow-soft">
                            <img src="/client/images/city_imgs/ninh-binh.jpg" alt="Ninh Bình"
                                class="h-14 w-20 rounded-lg object-cover">
                            <div>
                                <p class="text-sm font-bold text-slate-700">Ninh Bình</p>
                                <p class="text-xs text-slate-500">Thiên nhiên hùng vĩ, thư giãn</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft">
                    <h3 class="text-base font-extrabold text-slate-800">Trang phổ biến</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li>
                            <a href="{{ route('client.page.show', ['slug' => 'gioi-thieu']) }}"
                                class="inline-flex items-center gap-2 font-semibold text-primary-700 transition hover:translate-x-1 hover:text-primary-600">
                                <i class="fa-solid fa-circle-info text-xs" aria-hidden="true"></i>
                                Giới thiệu
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('client.page.show', ['slug' => 'chinh-sach']) }}"
                                class="inline-flex items-center gap-2 font-semibold text-primary-700 transition hover:translate-x-1 hover:text-primary-600">
                                <i class="fa-solid fa-file-shield text-xs" aria-hidden="true"></i>
                                Chính sách hỗ trợ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('client.home') }}"
                                class="inline-flex items-center gap-2 font-semibold text-primary-700 transition hover:translate-x-1 hover:text-primary-600">
                                <i class="fa-solid fa-house text-xs" aria-hidden="true"></i>
                                Trang chủ
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    {{-- FOOTER --}}
    {{-- Footer được render trong x-client.layout --}}
</x-client.layout>
