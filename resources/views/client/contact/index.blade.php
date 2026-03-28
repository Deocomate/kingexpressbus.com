{{-- ===== resources/views/client/contact/index.blade.php ===== --}}
<x-client.layout :title="$title" :description="$description" body-class="bg-[#F8FAFC] text-slate-800">
    @php
        $safeExternalUrl = static function (?string $url): ?string {
            if (!$url) {
                return null;
            }

            $trimmed = trim($url);
            return preg_match('/^https?:\/\//i', $trimmed) ? $trimmed : null;
        };

        $zaloUrl = $safeExternalUrl((string) data_get($webProfile, 'zalo_url'));
        $facebookUrl = $safeExternalUrl((string) data_get($webProfile, 'facebook_url'));

        $mapEmbedSrc = null;
        $rawMapEmbed = data_get($webProfile, 'map_embedded');
        if (is_string($rawMapEmbed) && preg_match('/src=["\']([^"\']+)["\']/i', $rawMapEmbed, $matches)) {
            $candidateMapSrc = html_entity_decode((string) $matches[1], ENT_QUOTES, 'UTF-8');
            if (preg_match('/^https?:\/\//i', $candidateMapSrc)) {
                $mapEmbedSrc = $candidateMapSrc;
            }
        }

        $faqItems = __('client.contact.faq_items');
        $faqItems = is_array($faqItems) ? $faqItems : [];

        $officeItems = is_iterable($offices ?? null) ? $offices : [];

        $contactChannels = [
            [
                'value' => data_get($webProfile, 'hotline'),
                'label' => __('client.contact.channels.hotline'),
                'href' => data_get($webProfile, 'hotline') ? 'tel:' . preg_replace('/[^\d+]/', '', (string) data_get($webProfile, 'hotline')) : null,
                'icon' => 'fa-solid fa-phone-volume',
                'tone' => 'from-amber-100 to-amber-50 text-amber-700 border-amber-200',
            ],
            [
                'value' => data_get($webProfile, 'phone'),
                'label' => __('client.contact.channels.care'),
                'href' => data_get($webProfile, 'phone') ? 'tel:' . preg_replace('/[^\d+]/', '', (string) data_get($webProfile, 'phone')) : null,
                'icon' => 'fa-solid fa-headset',
                'tone' => 'from-blue-100 to-blue-50 text-blue-700 border-blue-200',
            ],
            [
                'value' => data_get($webProfile, 'email'),
                'label' => __('client.contact.channels.email'),
                'href' => data_get($webProfile, 'email') ? 'mailto:' . data_get($webProfile, 'email') : null,
                'icon' => 'fa-regular fa-envelope',
                'tone' => 'from-emerald-100 to-emerald-50 text-emerald-700 border-emerald-200',
            ],
            [
                'value' => 'Facebook',
                'label' => __('client.contact.channels.facebook'),
                'href' => $facebookUrl,
                'icon' => 'fa-brands fa-facebook-f',
                'tone' => 'from-sky-100 to-sky-50 text-sky-700 border-sky-200',
            ],
            [
                'value' => 'Zalo',
                'label' => __('client.contact.channels.zalo'),
                'href' => $zaloUrl,
                'icon' => 'fa-solid fa-comments',
                'tone' => 'from-indigo-100 to-indigo-50 text-indigo-700 border-indigo-200',
            ],
            [
                'value' => data_get($webProfile, 'whatsapp'),
                'label' => __('client.contact.channels.whatsapp'),
                'href' => data_get($webProfile, 'whatsapp') ? 'https://wa.me/' . preg_replace('/[^\d]/', '', (string) data_get($webProfile, 'whatsapp')) : null,
                'icon' => 'fa-brands fa-whatsapp',
                'tone' => 'from-green-100 to-green-50 text-green-700 border-green-200',
            ],
        ];
    @endphp

    @push('styles')
        <style>
            @keyframes contact-hero-shift {
                0% {
                    background-position: center top;
                }

                100% {
                    background-position: center bottom;
                }
            }

            .contact-hero-bg {
                background-image:
                    linear-gradient(110deg, rgba(6, 22, 41, 0.78), rgba(255, 155, 0, 0.52)),
                    url('/client/images/city_imgs/ha-noi.jpg');
                background-size: cover;
                background-position: center;
                animation: contact-hero-shift 17s ease-in-out infinite alternate;
            }

            .contact-map-wrap {
                position: relative;
                overflow: hidden;
                border-radius: 1rem;
                min-height: 320px;
            }

            .contact-map-wrap iframe {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                border: 0;
            }

            @media (prefers-reduced-motion: reduce) {
                .contact-hero-bg {
                    animation: none;
                }
            }
        </style>
    @endpush

    {{-- HEADER --}}
    {{-- Header được render trong x-client.layout --}}

    {{-- HERO/SEARCH --}}
    <section class="contact-hero-bg relative overflow-visible px-4 pb-14 pt-24 md:pb-20 md:pt-28">
        <div class="container mx-auto max-w-7xl">
            <div class="max-w-3xl">
                <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-white">
                    <i class="fa-solid fa-headset" aria-hidden="true"></i>
                    {{ __('client.contact.hero.badge') }}
                </span>
                <h1 class="text-3xl font-extrabold leading-tight text-white sm:text-4xl lg:text-6xl">
                    {{ __('client.contact.hero.title') }}
                </h1>
                <p class="mt-4 max-w-2xl text-sm text-slate-100/95 sm:text-base lg:text-lg">
                    {{ __('client.contact.hero.subtitle') }}
                </p>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                @if (data_get($webProfile, 'hotline'))
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', (string) data_get($webProfile, 'hotline')) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-3 text-sm font-bold text-white shadow-soft transition hover:bg-primary-700 active:scale-95">
                        <i class="fa-solid fa-phone-volume" aria-hidden="true"></i>
                        {{ data_get($webProfile, 'hotline') }}
                    </a>
                @endif
                @if ($zaloUrl)
                    <a href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/20 active:scale-95">
                        <i class="fa-solid fa-comments" aria-hidden="true"></i>
                        {{ __('client.contact.hero.zalo_cta') }}
                    </a>
                @endif
            </div>

            <div class="relative z-50 mt-8" style="z-index: 90;">
                <x-client.search-bar :submit-label="__('client.contact.hero.search_submit')" />
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                <article class="rounded-2xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-wide text-white/80">{{ __('client.contact.hero.stats.routes') }}</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ number_format(data_get($stats, 'route_count', 0)) }}+</p>
                </article>
                <article class="rounded-2xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-wide text-white/80">{{ __('client.contact.hero.stats.fleet') }}</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ number_format(data_get($stats, 'bus_count', 0)) }}+</p>
                </article>
                <article class="rounded-2xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-wide text-white/80">{{ __('client.contact.hero.stats.trips') }}</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ number_format(data_get($stats, 'trip_count', 0)) }}+</p>
                </article>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl grid grid-cols-1 gap-6 lg:grid-cols-12">
            <article class="lg:col-span-7 rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-7">
                <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.contact.headings.support_channels') }}</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-3xl">{{ __('client.contact.support_title') }}</h2>
                <p class="mt-2 text-sm text-slate-500 md:text-base">{{ __('client.contact.support_desc') }}</p>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach ($contactChannels as $channel)
                        @if ($channel['href'])
                            <a href="{{ $channel['href'] }}"
                                @if (str_starts_with($channel['href'], 'http')) target="_blank" rel="noopener noreferrer" @endif
                                class="group rounded-2xl border bg-linear-to-br p-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl {{ $channel['tone'] }}">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/80 text-base">
                                        <i class="{{ $channel['icon'] }}" aria-hidden="true"></i>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-xs font-bold uppercase tracking-wide">{{ $channel['label'] }}</span>
                                        <span class="mt-1 block truncate text-sm font-bold">{{ $channel['value'] }}</span>
                                    </span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </article>

            <aside class="lg:col-span-5 space-y-6">
                <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.contact.headings.working_hours') }}</p>
                    <h3 class="mt-1 text-xl font-extrabold text-slate-800 md:text-2xl">{{ __('client.contact.working_hours_title') }}</h3>
                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                            <p class="text-sm font-semibold text-slate-800">{{ __('client.contact.hours.weekday_label') }}</p>
                            <p class="text-sm font-extrabold text-emerald-700">07:00 - 22:00</p>
                        </div>
                        <div class="flex items-center justify-between rounded-xl border border-blue-200 bg-blue-50 p-3">
                            <p class="text-sm font-semibold text-slate-800">{{ __('client.contact.hours.weekend_label') }}</p>
                            <p class="text-sm font-extrabold text-blue-700">08:00 - 21:00</p>
                        </div>
                        <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            {{ __('client.contact.hours.note') }}
                        </p>
                    </div>
                </article>

                <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.contact.headings.offices') }}</p>
                    <h3 class="mt-1 text-xl font-extrabold text-slate-800 md:text-2xl">{{ __('client.contact.offices_title') }}</h3>
                    <div class="mt-4 max-h-72 space-y-2 overflow-y-auto pr-1">
                        @forelse ($officeItems as $office)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:shadow-soft">
                                <p class="text-sm font-bold text-slate-800">{{ data_get($office, 'name', __('client.contact.office_fallback_name')) }}</p>
                                <p class="mt-1 text-xs text-slate-500 md:text-sm">
                                    {{ data_get($office, 'address', __('client.booking.common.updating')) }}, {{ data_get($office, 'district_name', '') }}, {{ data_get($office, 'province_name', '') }}
                                </p>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-slate-300 p-4 text-center text-sm text-slate-500">{{ __('client.contact.no_offices') }}</p>
                        @endforelse
                    </div>
                </article>
            </aside>
        </div>
    </section>

    <section class="bg-white px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl grid grid-cols-1 gap-6 lg:grid-cols-12">
            <article class="lg:col-span-6 rounded-2xl border border-amber-100 bg-[#fffdf8] p-5 shadow-soft md:p-7" x-data="{ openFaq: 0 }">
                <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.contact.faq_badge') }}</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-3xl">{{ __('client.contact.headings.faq') }}</h3>

                <div class="mt-5 space-y-3">
                    @foreach($faqItems as $index => $faq)
                        <div class="rounded-xl border border-amber-100 bg-white">
                            <button type="button" @click="openFaq = openFaq === {{ $index }} ? -1 : {{ $index }}"
                                id="contact-faq-trigger-{{ $index }}"
                                :aria-expanded="openFaq === {{ $index }}"
                                aria-controls="contact-faq-panel-{{ $index }}"
                                class="flex w-full items-center justify-between gap-3 p-4 text-left">
                                <span class="text-sm font-bold text-slate-800 md:text-base">{{ $faq['question'] }}</span>
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-700 transition" :class="openFaq === {{ $index }} ? 'rotate-180' : ''">
                                    <i class="fa-solid fa-chevron-down text-xs" aria-hidden="true"></i>
                                </span>
                            </button>
                            <div x-show="openFaq === {{ $index }}" x-cloak x-transition.opacity.duration.220ms
                                id="contact-faq-panel-{{ $index }}"
                                role="region"
                                aria-labelledby="contact-faq-trigger-{{ $index }}"
                                class="border-t border-amber-100 px-4 pb-4 pt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="lg:col-span-6 rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-7">
                <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.contact.headings.map') }}</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-3xl">{{ __('client.contact.map_desc') }}</h3>

                @if ($mapEmbedSrc)
                    <div class="contact-map-wrap mt-5 border border-amber-100 bg-slate-50 shadow-soft">
                        <iframe src="{{ $mapEmbedSrc }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="mt-5 rounded-2xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                        {{ __('client.contact.map_updating') }}
                    </div>
                @endif
            </article>
        </div>
    </section>

    <section class="px-4 pb-14 pt-12 md:pb-20">
        <div class="container mx-auto max-w-7xl">
            <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 shadow-soft">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_18%_14%,rgba(255,201,0,0.24),transparent_42%),radial-gradient(circle_at_85%_92%,rgba(255,155,0,0.28),transparent_44%)]"></div>
                <div class="relative grid items-center gap-6 p-6 text-white md:p-12 lg:grid-cols-[1.15fr_0.85fr]">
                    <div>
                        <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-200">{{ __('client.contact.cta.badge') }}</p>
                        <h3 class="mt-3 text-2xl font-extrabold leading-tight md:text-4xl">{{ __('client.contact.cta.title') }}</h3>
                        <p class="mt-4 max-w-2xl text-sm text-slate-200 md:text-base">{{ __('client.contact.cta.subtitle') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3 lg:justify-end">
                        <a href="{{ route('client.home') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-soft transition hover:bg-primary-700 active:scale-95">
                            <i class="fa-solid fa-ticket" aria-hidden="true"></i>
                            {{ __('client.contact.cta.book_button') }}
                        </a>
                        @if (data_get($webProfile, 'hotline'))
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', (string) data_get($webProfile, 'hotline')) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/20 active:scale-95">
                                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                {{ __('client.contact.cta.call_button') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    {{-- Footer được render trong x-client.layout --}}
</x-client.layout>
