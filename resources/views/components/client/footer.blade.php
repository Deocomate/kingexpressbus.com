@php
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = \App\Helpers\SystemHelper::mediaUrl(data_get($webProfile, 'logo_url'), \App\Helpers\SystemHelper::mediaUrl('/assets/client/images/web-information/logo.jpg'));

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

<footer class="kx-footer">
    <div class="container mx-auto max-w-7xl px-4 py-10 md:py-12">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <a href="{{ route('client.home') }}" class="mb-4 inline-flex items-center gap-3">
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-11 w-11 rounded-sm border border-white/15 object-cover">
                    <span class="text-lg font-extrabold tracking-tight text-white">{{ $brandTitle }}</span>
                </a>
                <p class="kx-footer-text mb-4">
                    {{ data_get($webProfile, 'description', __('client.footer.default_description')) }}
                </p>
                <div class="flex items-center gap-2">
                    @if(data_get($webProfile, 'facebook_url'))
                        <a href="{{ data_get($webProfile, 'facebook_url') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
                            class="kx-footer-social">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'zalo_url'))
                        <a href="{{ data_get($webProfile, 'zalo_url') }}" target="_blank" rel="noopener noreferrer" aria-label="Zalo"
                            class="kx-footer-social">
                            <i class="fa-solid fa-comment-dots text-sm"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'whatsapp'))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', data_get($webProfile, 'whatsapp')) }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
                            class="kx-footer-social">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'email'))
                        <a href="mailto:{{ data_get($webProfile, 'email') }}" aria-label="Email"
                            class="kx-footer-social">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="kx-footer-heading">{{ __('client.footer.about') }}</h4>
                <ul class="space-y-3">
                    @foreach($aboutLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="kx-footer-link">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="kx-footer-heading">{{ __('client.footer.support') }}</h4>
                <ul class="space-y-3">
                    @foreach($supportLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="kx-footer-link">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="kx-footer-heading">{{ __('client.footer.contact') }}</h4>
                <ul class="space-y-3 text-sm text-white/70">
                    @if(data_get($webProfile, 'address'))
                        <li class="flex items-start gap-2.5">
                            <i class="fa-solid fa-location-dot kx-footer-contact-icon"></i>
                            <span class="leading-relaxed">{{ data_get($webProfile, 'address') }}</span>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'hotline'))
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-headset kx-footer-contact-icon"></i>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', data_get($webProfile, 'hotline')) }}" class="kx-footer-hotline">
                                {{ data_get($webProfile, 'hotline') }}
                            </a>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'phone'))
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-phone kx-footer-contact-icon"></i>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', data_get($webProfile, 'phone')) }}" class="transition-colors duration-fast ease-out-soft hover:text-white">
                                {{ data_get($webProfile, 'phone') }}
                            </a>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'email'))
                        <li class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope kx-footer-contact-icon"></i>
                            <a href="mailto:{{ data_get($webProfile, 'email') }}" class="transition-colors duration-fast ease-out-soft hover:text-white">
                                {{ data_get($webProfile, 'email') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-8 border-t border-white/10 pt-4">
            <div class="flex flex-col gap-3 text-sm text-white/50 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ date('Y') }} {{ $brandTitle }}. {{ __('client.footer.rights') }}</p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('client.about') }}" class="transition-colors duration-fast ease-out-soft hover:text-brand-500">{{ __('client.footer.links.intro') }}</a>
                    <a href="{{ route('client.contact') }}" class="transition-colors duration-fast ease-out-soft hover:text-brand-500">{{ __('client.footer.links.contact') }}</a>
                    <a href="{{ route('client.routes.index') }}" class="transition-colors duration-fast ease-out-soft hover:text-brand-500">{{ __('client.footer.links.routes') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
