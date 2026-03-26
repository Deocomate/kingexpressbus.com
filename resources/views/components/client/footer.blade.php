@php
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = data_get($webProfile, 'logo_url', '/client/images/web information/logo.jpg');

    $aboutLinks = [
        ['label' => __('client.footer.links.intro'), 'url' => route('client.about')],
        ['label' => __('client.footer.links.routes'), 'url' => route('client.routes.index')],
        ['label' => __('client.footer.links.contact'), 'url' => route('client.contact')],
    ];

    $supportLinks = [
        ['label' => __('client.footer.support_links.cancellation'), 'url' => route('client.about') . '#faq'],
        ['label' => __('client.footer.support_links.terms'), 'url' => route('client.about')],
        ['label' => __('client.footer.support_links.privacy'), 'url' => route('client.about')],
    ];
@endphp

<footer class="relative overflow-hidden bg-slate-900 text-slate-100">
    <div class="absolute inset-0 bg-linear-to-br from-slate-900 via-slate-900 to-[#3f2a00]"></div>
    <div class="relative container mx-auto max-w-7xl px-4 py-14 md:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <a href="{{ route('client.home') }}" class="mb-5 inline-flex items-center gap-3">
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-11 w-11 rounded-2xl object-cover ring-1 ring-amber-200/20">
                    <span class="text-lg font-extrabold tracking-tight text-white">{{ $brandTitle }}</span>
                </a>
                <p class="mb-6 text-sm leading-relaxed text-slate-300">
                    {{ data_get($webProfile, 'description', __('client.footer.default_description')) }}
                </p>
                <div class="flex items-center gap-2">
                    @if(data_get($webProfile, 'facebook_url'))
                        <a href="{{ data_get($webProfile, 'facebook_url') }}" target="_blank" aria-label="Facebook"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 transition hover:-translate-y-1 hover:bg-primary-600 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'zalo_url'))
                        <a href="{{ data_get($webProfile, 'zalo_url') }}" target="_blank" aria-label="Zalo"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 transition hover:-translate-y-1 hover:bg-primary-600 hover:text-white">
                            <i class="fa-solid fa-comment-dots"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'whatsapp'))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', data_get($webProfile, 'whatsapp')) }}" target="_blank" aria-label="WhatsApp"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 transition hover:-translate-y-1 hover:bg-primary-600 hover:text-white">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'email'))
                        <a href="mailto:{{ data_get($webProfile, 'email') }}" aria-label="Email"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 transition hover:-translate-y-1 hover:bg-primary-600 hover:text-white">
                            <i class="fa-solid fa-envelope"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="mb-4 text-base font-bold text-white">{{ __('client.footer.about') }}</h4>
                <ul class="space-y-3 text-sm">
                    @foreach($aboutLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="text-slate-300 transition hover:text-accent">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-base font-bold text-white">{{ __('client.footer.support') }}</h4>
                <ul class="space-y-3 text-sm">
                    @foreach($supportLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="text-slate-300 transition hover:text-accent">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="mb-4 text-base font-bold text-white">{{ __('client.footer.contact') }}</h4>
                <ul class="space-y-3 text-sm text-slate-300">
                    @if(data_get($webProfile, 'address'))
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot mt-1 text-primary-500"></i>
                            <span class="leading-relaxed">{{ data_get($webProfile, 'address') }}</span>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'hotline'))
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-headset text-primary-500"></i>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', data_get($webProfile, 'hotline')) }}" class="font-bold text-accent transition hover:text-white">
                                {{ data_get($webProfile, 'hotline') }}
                            </a>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'phone'))
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-primary-500"></i>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', data_get($webProfile, 'phone')) }}" class="transition hover:text-white">
                                {{ data_get($webProfile, 'phone') }}
                            </a>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'email'))
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-primary-500"></i>
                            <a href="mailto:{{ data_get($webProfile, 'email') }}" class="transition hover:text-white">
                                {{ data_get($webProfile, 'email') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-10 border-t border-white/10 pt-5">
            <div class="flex flex-col gap-3 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ date('Y') }} {{ $brandTitle }}. {{ __('client.footer.rights') }}</p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('client.about') }}" class="transition hover:text-accent">{{ __('client.footer.links.intro') }}</a>
                    <a href="{{ route('client.contact') }}" class="transition hover:text-accent">{{ __('client.footer.links.contact') }}</a>
                    <a href="{{ route('client.routes.index') }}" class="transition hover:text-accent">{{ __('client.footer.links.routes') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
