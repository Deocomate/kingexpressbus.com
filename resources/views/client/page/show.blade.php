<x-client.layout :title="$title ?? __('client.page.meta_title')" :description="$description ?? ''" body-class="bg-page text-ink">
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
                    linear-gradient(180deg, rgba(4, 17, 31, 0.58), rgba(4, 17, 31, 0.78)),
                    url('/assets/client/images/city_imgs/da-nang.jpg');
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
    <section class="page-hero-bg ksb-section-hero relative z-elevated overflow-visible px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="max-w-3xl">
                <span
                    class="mb-4 inline-flex items-center gap-2 rounded-sm border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-white">
                    <i class="fa-solid fa-sparkles" aria-hidden="true"></i>
                    {{ __('client.page_view.hero.badge') }}
                </span>
                <h1 class="font-display text-3xl font-extrabold leading-tight text-white sm:text-4xl lg:text-5xl">
                    {{ $page->title ?? __('client.page_view.hero.fallback_title') }}
                </h1>
                <p class="mt-4 text-sm text-slate-100/95 sm:text-base lg:text-lg">
                    {{ __('client.page_view.hero.description') }}
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @if ($displayUpdatedAt)
                        <span class="inline-flex items-center gap-2 rounded-sm bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            {{ __('client.page_view.hero.updated_at', ['datetime' => $displayUpdatedAt]) }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-2 rounded-sm bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                        <i class="fa-solid fa-shield-heart" aria-hidden="true"></i>
                        {{ __('client.page_view.hero.trust_note') }}
                    </span>
                </div>
            </div>

            <div class="ksb-hero-search mt-8">
                <x-client.search-bar :submit-label="__('client.page_view.hero.search_submit')" />
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="ksb-section px-4">
        <div class="container mx-auto max-w-7xl grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8">
            <article class="lg:col-span-8 space-y-5 rounded-sm border border-amber-100 bg-white p-5  md:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-100 pb-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-primary-600">{{ __('client.page_view.content.label') }}</p>
                        <h2 class="mt-1 text-xl font-extrabold text-slate-800 md:text-2xl">{{ $page->title ?? __('client.page_view.content.fallback_title') }}</h2>
                    </div>
                    <a href="{{ route('client.routes.index') }}"
                        class="inline-flex items-center gap-2 rounded-sm border border-primary-600/25 bg-primary-50 px-4 py-2 text-sm font-bold text-primary-700 transition-all duration-300  hover:bg-primary-600 hover:text-white ">
                        {{ __('client.page_view.content.cta_book_now') }}
                        <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="page-content-prose kx-prose max-w-none">
                    {!! \App\Support\Admin\HtmlSanitizer::sanitize($page->content ?? '') !!}
                </div>
            </article>

            <aside class="lg:col-span-4 space-y-5">
                <div
                    class="rounded-sm border border-amber-100 bg-white p-5  transition-all duration-300  hover:shadow-xl">
                    <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-sm bg-primary-50 text-primary-600">
                        <i class="fa-solid fa-headset text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-800">{{ __('client.page_view.support.title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ __('client.page_view.support.description') }}
                    </p>
                    <a href="{{ route('client.contact') }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-sm bg-primary-600 px-4 py-2.5 text-sm font-bold text-white  transition-all duration-300 hover:bg-primary-700 ">
                        {{ __('client.page_view.support.cta') }}
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="rounded-sm border border-amber-100 bg-white p-5 ">
                    <h3 class="text-base font-extrabold text-slate-800">{{ __('client.page_view.destinations.title') }}</h3>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('client.routes.index') }}"
                            class="group flex items-center gap-3 rounded-sm border border-slate-100 p-2 transition-all duration-300  hover:border-primary-200 hover:">
                            <img src="/assets/client/images/city_imgs/sapa.jpg" alt="Sa Pa"
                                class="h-14 w-20 rounded-sm object-cover">
                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ __('client.page_view.destinations.sapa.name') }}</p>
                                <p class="text-xs text-slate-500">{{ __('client.page_view.destinations.sapa.desc') }}</p>
                            </div>
                        </a>
                        <a href="{{ route('client.routes.index') }}"
                            class="group flex items-center gap-3 rounded-sm border border-slate-100 p-2 transition-all duration-300  hover:border-primary-200 hover:">
                            <img src="/assets/client/images/city_imgs/ha-noi.jpg" alt="Hà Nội"
                                class="h-14 w-20 rounded-sm object-cover">
                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ __('client.page_view.destinations.hanoi.name') }}</p>
                                <p class="text-xs text-slate-500">{{ __('client.page_view.destinations.hanoi.desc') }}</p>
                            </div>
                        </a>
                        <a href="{{ route('client.routes.index') }}"
                            class="group flex items-center gap-3 rounded-sm border border-slate-100 p-2 transition-all duration-300  hover:border-primary-200 hover:">
                            <img src="/assets/client/images/city_imgs/ninh-binh.jpg" alt="Ninh Bình"
                                class="h-14 w-20 rounded-sm object-cover">
                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ __('client.page_view.destinations.ninh_binh.name') }}</p>
                                <p class="text-xs text-slate-500">{{ __('client.page_view.destinations.ninh_binh.desc') }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="rounded-sm border border-amber-100 bg-white p-5 ">
                    <h3 class="text-base font-extrabold text-slate-800">{{ __('client.page_view.popular_pages.title') }}</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li>
                            <a href="{{ route('client.page.show', ['slug' => 'gioi-thieu']) }}"
                                class="inline-flex items-center gap-2 font-semibold text-primary-700 transition hover:translate-x-1 hover:text-primary-600">
                                <i class="fa-solid fa-circle-info text-xs" aria-hidden="true"></i>
                                {{ __('client.page_view.popular_pages.about') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('client.page.show', ['slug' => 'chinh-sach']) }}"
                                class="inline-flex items-center gap-2 font-semibold text-primary-700 transition hover:translate-x-1 hover:text-primary-600">
                                <i class="fa-solid fa-file-shield text-xs" aria-hidden="true"></i>
                                {{ __('client.page_view.popular_pages.policy') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('client.home') }}"
                                class="inline-flex items-center gap-2 font-semibold text-primary-700 transition hover:translate-x-1 hover:text-primary-600">
                                <i class="fa-solid fa-house text-xs" aria-hidden="true"></i>
                                {{ __('client.page_view.popular_pages.home') }}
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
