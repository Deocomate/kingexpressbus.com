{{-- ===== resources/views/client/contact/index.blade.php ===== --}}
<x-client.layout :title="$title" :description="$description" body-class="bg-neutral-50">

    @push('styles')
        <style>
            /* Hero Background */
            .hero-contact-bg {
                background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.8)), url('/client/images/city_imgs/ha-noi.jpg');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }

            @media (max-width: 768px) {
                .hero-contact-bg {
                    background-attachment: scroll;
                }
            }

            /* Contact Card Hover */
            .contact-card {
                transition: box-shadow 0.2s ease, border-color 0.2s ease;
            }

            .contact-card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            /* FAQ Item */
            .faq-item {
                transition: background-color 0.2s ease;
            }

            .faq-item:hover {
                background-color: #f9fafb;
            }

            /* Office Card */
            .office-card {
                transition: border-color 0.2s ease;
            }

            .office-card:hover {
                border-color: #D97706;
            }

            /* Map Container */
            .map-container {
                position: relative;
                padding-bottom: 50%;
                height: 0;
                overflow: hidden;
                border-radius: 8px;
            }

            .map-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: 0;
            }

            /* Pulse Effect for Online Status */
            .pulse-dot {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
                }
            }
        </style>
    @endpush

    {{-- Hero Section --}}
    <section
        class="relative min-h-[350px] md:min-h-[400px] hero-contact-bg flex flex-col justify-center items-center px-4 py-16 pt-24 md:py-20">
        <div class="container relative z-10 w-full max-w-4xl text-center">
            {{-- Badge --}}
            <span
                class="inline-block py-1.5 px-4 rounded-md bg-accent-500/20 text-accent-300 border border-accent-400/30 text-xs md:text-sm font-semibold tracking-wider mb-3 md:mb-4">
                <i class="fa-solid fa-headset mr-2"></i>{{ __('client.contact.hero.badge') }}
            </span>

            {{-- Hero Title --}}
            <h1
                class="text-3xl sm:text-4xl md:text-5xl font-semibold text-white leading-tight mb-3 md:mb-4">
                {{ __('client.contact.hero.title') }}
            </h1>

            {{-- Subtitle --}}
            <p class="text-base md:text-lg lg:text-xl text-neutral-200 max-w-2xl mx-auto px-2">
                {{ __('client.contact.hero.subtitle') }}
            </p>

            {{-- Quick Contact Buttons --}}
            <div class="flex flex-wrap justify-center gap-3 md:gap-4 mt-6 md:mt-8">
                @if($webProfile->hotline ?? null)
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $webProfile->hotline) }}"
                        class="inline-flex items-center gap-2 px-5 md:px-6 py-2.5 md:py-3 bg-accent-500 text-white rounded-md font-semibold text-sm md:text-base hover:bg-accent-600 transition-colors duration-200">
                        <i class="fa-solid fa-phone"></i>
                        {{ $webProfile->hotline }}
                    </a>
                @endif
                @if($webProfile->zalo_url ?? null)
                    <a href="{{ $webProfile->zalo_url }}" target="_blank"
                        class="inline-flex items-center gap-2 px-5 md:px-6 py-2.5 md:py-3 bg-primary-600 text-white rounded-md font-semibold text-sm md:text-base hover:bg-primary-700 transition-colors duration-200">
                        <i class="fa-solid fa-comment-dots"></i>
                        Chat Zalo
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Support Channels Section --}}
    <section class="py-12 md:py-16 bg-white relative -mt-6 md:-mt-8 z-20">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-card p-6 md:p-8 lg:p-12 border border-neutral-200">
                {{-- Section Header --}}
                <div class="text-center mb-8 md:mb-10">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-900 mb-2 md:mb-3">
                        {{ __('client.contact.headings.support_channels') }}
                    </h2>
                    <p class="text-neutral-500 text-sm md:text-base max-w-xl mx-auto">
                        {{ __('client.contact.support_desc') }}</p>
                </div>

                {{-- Support Channels Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    {{-- Hotline --}}
                    @if($webProfile->hotline ?? null)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $webProfile->hotline) }}"
                            class="contact-card group flex items-center p-4 md:p-5 bg-accent-50 rounded-lg border border-accent-200 hover:border-accent-400">
                            <div
                                class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 bg-accent-500 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-phone text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 md:ml-4 min-w-0">
                                <p class="font-semibold text-base md:text-lg text-neutral-900">
                                    {{ __('client.contact.channels.hotline') }}</p>
                                <p class="text-accent-600 font-semibold text-sm md:text-base truncate">
                                    {{ $webProfile->hotline }}</p>
                            </div>
                            <div class="ml-auto shrink-0">
                                <span class="w-2.5 h-2.5 md:w-3 md:h-3 bg-green-500 rounded-full block pulse-dot"></span>
                            </div>
                        </a>
                    @endif

                    {{-- Phone --}}
                    @if($webProfile->phone ?? null)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $webProfile->phone) }}"
                            class="contact-card group flex items-center p-4 md:p-5 bg-primary-50 rounded-lg border border-primary-200 hover:border-primary-400">
                            <div
                                class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 bg-primary-600 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-headset text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 md:ml-4 min-w-0">
                                <p class="font-semibold text-base md:text-lg text-neutral-900">
                                    {{ __('client.contact.channels.care') }}</p>
                                <p class="text-primary-600 font-semibold text-sm md:text-base truncate">
                                    {{ $webProfile->phone }}</p>
                            </div>
                        </a>
                    @endif

                    {{-- Email --}}
                    @if($webProfile->email ?? null)
                        <a href="mailto:{{ $webProfile->email }}"
                            class="contact-card group flex items-center p-4 md:p-5 bg-green-50 rounded-lg border border-green-200 hover:border-green-400">
                            <div
                                class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 bg-green-600 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-regular fa-envelope text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 md:ml-4 min-w-0">
                                <p class="font-semibold text-base md:text-lg text-neutral-900">
                                    {{ __('client.contact.channels.email') }}</p>
                                <p class="text-green-600 font-semibold text-xs md:text-sm truncate">{{ $webProfile->email }}
                                </p>
                            </div>
                        </a>
                    @endif

                    {{-- Facebook --}}
                    @if($webProfile->facebook_url ?? null)
                        <a href="{{ $webProfile->facebook_url }}" target="_blank"
                            class="contact-card group flex items-center p-4 md:p-5 bg-blue-50 rounded-lg border border-blue-200 hover:border-blue-400">
                            <div
                                class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-brands fa-facebook-f text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 md:ml-4 min-w-0">
                                <p class="font-semibold text-base md:text-lg text-neutral-900">
                                    {{ __('client.contact.channels.facebook') }}</p>
                                <p class="text-blue-600 text-xs md:text-sm">Facebook Fanpage</p>
                            </div>
                        </a>
                    @endif

                    {{-- Zalo --}}
                    @if($webProfile->zalo_url ?? null)
                        <a href="{{ $webProfile->zalo_url }}" target="_blank"
                            class="contact-card group flex items-center p-4 md:p-5 bg-blue-50 rounded-lg border border-blue-200 hover:border-blue-400">
                            <div
                                class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 bg-blue-500 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-comment-dots text-lg md:text-xl"></i>
                            </div>
                            <div class="ml-3 md:ml-4 min-w-0">
                                <p class="font-semibold text-base md:text-lg text-neutral-900">
                                    {{ __('client.contact.channels.zalo') }}</p>
                                <p class="text-blue-600 text-xs md:text-sm">Chat ngay</p>
                            </div>
                        </a>
                    @endif

                    {{-- WhatsApp --}}
                    @if($webProfile->whatsapp ?? null)
                        <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $webProfile->whatsapp) }}" target="_blank"
                            class="contact-card group flex items-center p-4 md:p-5 bg-green-50 rounded-lg border border-green-200 hover:border-green-400">
                            <div
                                class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 bg-green-600 text-white rounded-lg flex items-center justify-center">
                                <i class="fa-brands fa-whatsapp text-xl md:text-2xl"></i>
                            </div>
                            <div class="ml-3 md:ml-4 min-w-0">
                                <p class="font-semibold text-base md:text-lg text-neutral-900">WhatsApp</p>
                                <p class="text-green-600 text-xs md:text-sm truncate">{{ $webProfile->whatsapp }}</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Working Hours & Offices Section --}}
    <section class="py-12 md:py-16 bg-neutral-50">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-6 md:gap-8">
                {{-- Working Hours --}}
                <div class="bg-white rounded-lg shadow-card p-6 md:p-8 border border-neutral-200">
                    <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
                        <div
                            class="w-12 h-12 md:w-14 md:h-14 rounded-lg bg-accent-500 flex items-center justify-center text-white">
                            <i class="fa-regular fa-clock text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-semibold text-neutral-900">
                            {{ __('client.contact.headings.working_hours') }}
                        </h3>
                    </div>

                    <div class="space-y-3 md:space-y-4">
                        <div
                            class="flex items-center justify-between p-3 md:p-4 bg-green-50 rounded-md border border-green-100">
                            <div class="flex items-center gap-2 md:gap-3">
                                <span class="w-2.5 h-2.5 md:w-3 md:h-3 bg-green-500 rounded-full pulse-dot"></span>
                                <span
                                    class="font-semibold text-neutral-800 text-sm md:text-base">{{ __('client.contact.hours.weekday_label') }}</span>
                            </div>
                            <span class="text-green-600 font-semibold text-sm md:text-base">07:00 - 22:00</span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 md:p-4 bg-primary-50 rounded-md border border-primary-100">
                            <div class="flex items-center gap-2 md:gap-3">
                                <span class="w-2.5 h-2.5 md:w-3 md:h-3 bg-primary-500 rounded-full"></span>
                                <span
                                    class="font-semibold text-neutral-800 text-sm md:text-base">{{ __('client.contact.hours.weekend_label') }}</span>
                            </div>
                            <span class="text-primary-600 font-semibold text-sm md:text-base">08:00 - 21:00</span>
                        </div>

                        <div
                            class="mt-4 md:mt-6 p-3 md:p-4 bg-accent-50 rounded-md border border-accent-200">
                            <p class="text-xs md:text-sm text-accent-800">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                {{ __('client.contact.hours.note') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Offices --}}
                <div class="bg-white rounded-lg shadow-card p-6 md:p-8 border border-neutral-200">
                    <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
                        <div
                            class="w-12 h-12 md:w-14 md:h-14 rounded-lg bg-primary-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-location-dot text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-semibold text-neutral-900">
                            {{ __('client.contact.headings.offices') }}</h3>
                    </div>

                    <div class="space-y-2 md:space-y-3 max-h-[280px] md:max-h-[350px] overflow-y-auto pr-2">
                        @forelse ($offices as $office)
                            <div
                                class="office-card p-3 md:p-4 bg-neutral-50 rounded-md border border-neutral-200">
                                <div class="flex items-start gap-2 md:gap-3">
                                    <div
                                        class="w-8 h-8 md:w-10 md:h-10 rounded-md bg-primary-100 text-primary-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid fa-building text-xs md:text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-neutral-900 text-sm md:text-base truncate">{{ $office->name }}
                                        </p>
                                        <p class="text-xs md:text-sm text-neutral-600 line-clamp-2">
                                            {{ $office->address }}, {{ $office->district_name }},
                                            {{ $office->province_name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-neutral-500 text-center py-6 md:py-8 text-sm md:text-base">
                                {{ __('client.contact.no_offices') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="py-12 md:py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                {{-- Section Header --}}
                <div class="text-center mb-8 md:mb-12">
                    <span
                        class="inline-block py-1.5 px-4 rounded-md bg-primary-50 text-primary-700 font-semibold text-xs md:text-sm border border-primary-200 mb-3 md:mb-4">
                        <i class="fa-solid fa-circle-question mr-2"></i>FAQ
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-900">
                        {{ __('client.contact.headings.faq') }}
                    </h2>
                </div>

                {{-- FAQ Accordion --}}
                <div class="space-y-3 md:space-y-4" x-data="{ openFaq: null }">
                    @foreach(__('client.contact.faq_items') as $index => $faq)
                        <div class="faq-item bg-white rounded-lg border border-neutral-200 overflow-hidden shadow-soft">
                            <button @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}"
                                class="w-full flex items-center justify-between p-4 md:p-5 text-left"
                                :class="openFaq === {{ $index }} ? 'bg-accent-50' : ''">
                                <span
                                    class="font-semibold text-neutral-900 pr-3 md:pr-4 text-sm md:text-base">{{ $faq['question'] }}</span>
                                <span
                                    class="shrink-0 w-7 h-7 md:w-8 md:h-8 rounded-md flex items-center justify-center transition-all"
                                    :class="openFaq === {{ $index }} ? 'bg-accent-500 text-white rotate-180' : 'bg-neutral-100 text-neutral-500'">
                                    <i class="fa-solid fa-chevron-down text-xs md:text-sm"></i>
                                </span>
                            </button>
                            <div x-show="openFaq === {{ $index }}" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="px-4 md:px-5 pb-4 md:pb-5 text-neutral-600 leading-relaxed border-t border-neutral-100">
                                <p class="pt-3 md:pt-4 text-sm md:text-base">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Map Section --}}
    @if($webProfile->map_embedded ?? null)
        <section class="py-12 md:py-16 bg-neutral-50">
            <div class="container mx-auto px-4">
                <div class="max-w-5xl mx-auto">
                    <div class="text-center mb-6 md:mb-8">
                        <h2 class="text-2xl md:text-3xl font-semibold text-neutral-900 mb-2 md:mb-3">
                            {{ __('client.contact.headings.map') }}</h2>
                        <p class="text-neutral-500 text-sm md:text-base">{{ __('client.contact.map_desc') }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-card p-3 md:p-4 border border-neutral-200">
                        <div class="map-container rounded-lg overflow-hidden">
                            {!! $webProfile->map_embedded !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- CTA Section --}}
    <section class="py-12 md:py-16 bg-accent-500">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8 text-center md:text-left">
                <div class="space-y-2 md:space-y-3 max-w-xl">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-white">
                        {{ __('client.contact.cta.title') }}
                    </h2>
                    <p class="text-white/80 text-base md:text-lg">
                        {{ __('client.contact.cta.subtitle') }}
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 w-full md:w-auto">
                    <a href="{{ route('client.home') }}"
                        class="inline-flex items-center justify-center gap-2 md:gap-3 px-6 md:px-8 py-3 md:py-4 bg-neutral-900 text-white rounded-md font-semibold text-base md:text-lg hover:bg-neutral-800 transition-colors duration-200">
                        <i class="fa-solid fa-ticket"></i>
                        {{ __('client.contact.cta.book_button') }}
                    </a>
                    @if($webProfile->hotline ?? null)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $webProfile->hotline) }}"
                            class="inline-flex items-center justify-center gap-2 md:gap-3 px-6 md:px-8 py-3 md:py-4 bg-white text-neutral-900 rounded-md font-semibold text-base md:text-lg hover:bg-neutral-100 transition-colors duration-200">
                            <i class="fa-solid fa-phone"></i>
                            {{ __('client.contact.cta.call_button') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

</x-client.layout>
