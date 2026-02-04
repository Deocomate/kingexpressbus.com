@php
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = data_get($webProfile, 'logo_url');

    // Footer navigation links
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

<footer class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
    {{-- Main Footer --}}
    <div class="container mx-auto px-4 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">

            {{-- Brand Column --}}
            <div class="lg:col-span-1">
                <a href="{{ route('client.home') }}" class="inline-flex items-center gap-2 mb-5">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10 rounded">
                    @endif
                    <span class="text-xl font-bold text-white">{{ $brandTitle }}</span>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    {{ data_get($webProfile, 'description', __('client.footer.default_description')) }}
                </p>

                {{-- Social Links --}}
                <div class="flex items-center gap-3">
                    @if(data_get($webProfile, 'facebook_url'))
                        <a href="{{ data_get($webProfile, 'facebook_url') }}" target="_blank" aria-label="Facebook"
                            class="w-10 h-10 rounded-full bg-slate-700 hover:bg-blue-600 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'zalo_url'))
                        <a href="{{ data_get($webProfile, 'zalo_url') }}" target="_blank" aria-label="Zalo"
                            class="w-10 h-10 rounded-full bg-slate-700 hover:bg-blue-500 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-300">
                            <i class="fa-solid fa-comment-dots"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'whatsapp'))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', data_get($webProfile, 'whatsapp')) }}"
                            target="_blank" aria-label="WhatsApp"
                            class="w-10 h-10 rounded-full bg-slate-700 hover:bg-green-500 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-300">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    @endif
                    @if(data_get($webProfile, 'email'))
                        <a href="mailto:{{ data_get($webProfile, 'email') }}" aria-label="Email"
                            class="w-10 h-10 rounded-full bg-slate-700 hover:bg-yellow-500 flex items-center justify-center text-gray-400 hover:text-white transition-all duration-300">
                            <i class="fas fa-envelope"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- About Links --}}
            <div>
                <h4 class="font-bold text-lg mb-5 text-yellow-400 flex items-center gap-2">
                    <i class="fa-solid fa-building text-sm"></i>
                    {{ __('client.footer.about') }}
                </h4>
                <ul class="space-y-3">
                    @foreach($aboutLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                                class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-right text-xs text-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Support Links --}}
            <div>
                <h4 class="font-bold text-lg mb-5 text-yellow-400 flex items-center gap-2">
                    <i class="fa-solid fa-headset text-sm"></i>
                    {{ __('client.footer.support') }}
                </h4>
                <ul class="space-y-3">
                    @foreach($supportLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                                class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center gap-2 group">
                                <i
                                    class="fa-solid fa-chevron-right text-xs text-yellow-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h4 class="font-bold text-lg mb-5 text-yellow-400 flex items-center gap-2">
                    <i class="fa-solid fa-phone text-sm"></i>
                    {{ __('client.footer.contact') }}
                </h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    @if(data_get($webProfile, 'address'))
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas fa-map-marker-alt text-yellow-400 text-xs"></i>
                            </div>
                            <span class="leading-relaxed">{{ data_get($webProfile, 'address') }}</span>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'hotline'))
                        <li class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-headset text-white text-xs"></i>
                            </div>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', data_get($webProfile, 'hotline')) }}"
                                class="hover:text-white transition-colors font-semibold text-green-400">
                                {{ data_get($webProfile, 'hotline') }}
                                <span class="text-xs text-gray-500 block">{{ __('client.footer.hotline_label') }}</span>
                            </a>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'phone'))
                        <li class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-phone-alt text-yellow-400 text-xs"></i>
                            </div>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', data_get($webProfile, 'phone')) }}"
                                class="hover:text-white transition-colors">
                                {{ data_get($webProfile, 'phone') }}
                            </a>
                        </li>
                    @endif
                    @if(data_get($webProfile, 'email'))
                        <li class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-envelope text-yellow-400 text-xs"></i>
                            </div>
                            <a href="mailto:{{ data_get($webProfile, 'email') }}"
                                class="hover:text-white transition-colors">
                                {{ data_get($webProfile, 'email') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-slate-700">
        <div class="container mx-auto px-4 py-5">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} {{ $brandTitle }}. {{ __('client.footer.rights') }}
                </p>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <a href="{{ route('client.about') }}"
                        class="hover:text-white transition-colors">{{ __('client.footer.links.intro') }}</a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ route('client.contact') }}"
                        class="hover:text-white transition-colors">{{ __('client.footer.links.contact') }}</a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ route('client.routes.index') }}"
                        class="hover:text-white transition-colors">{{ __('client.footer.links.routes') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
