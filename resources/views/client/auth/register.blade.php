<x-client.layout :title="__('client.auth.register.meta_title')" :description="__('client.auth.register.meta_description')">
    @php
        $redirectTarget = $redirectTo ?? route('client.profile.index');
    @endphp

    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-page px-4 py-12 sm:px-6 lg:px-8">

        <!-- Main Container -->
        <div class="relative z-10 w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">

            <!-- Left Side: Welcome Info (Hidden on Mobile) -->
            <div class="hidden lg:block space-y-8 pr-10 text-ink">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-sm text-xs font-semibold bg-brand-600 text-white uppercase tracking-wide">
                        <i class="fa-solid fa-gift mr-2"></i> {{ __('client.auth.register.badge') }}
                    </span>
                    <h1 class="mt-6 text-5xl font-semibold leading-tight tracking-tight">
                        {{ __('client.auth.register.welcome_title') }} <br>
                        <span class="text-accent-500">{{ __('client.auth.register.welcome_brand') }}</span>
                    </h1>
                    <p class="mt-4 text-lg leading-8 text-muted">
                        {{ __('client.auth.register.welcome_description') }}
                    </p>
                </div>

                <div class="grid gap-5 mt-10">
                    <div class="kx-panel flex items-start gap-4 p-4">
                        <div class="shrink-0 w-12 h-12 rounded-sm bg-accent-500 flex items-center justify-center text-white">
                            <i class="fa-solid fa-coins text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">{{ __('client.auth.register.feature_points_title') }}</h3>
                            <p class="text-sm text-muted mt-1">{{ __('client.auth.register.feature_points_desc') }}</p>
                        </div>
                    </div>

                    <div class="kx-panel flex items-start gap-4 p-4">
                        <div class="shrink-0 w-12 h-12 rounded-sm bg-brand-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-tags text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">{{ __('client.auth.register.feature_offers_title') }}</h3>
                            <p class="text-sm text-muted mt-1">{{ __('client.auth.register.feature_offers_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Register Form -->
            <div class="kx-panel-strong p-8 md:p-10 w-full max-w-lg mx-auto lg:ml-auto relative overflow-hidden">
                <!-- Decorative top border -->
                <div class="absolute top-0 left-0 w-full h-0.5 bg-accent-500"></div>

                <div class="text-center mb-8">
                    <h2 class="text-3xl font-semibold text-neutral-800 tracking-tight">{{ __('client.auth.register.form_title') }}</h2>
                    <p class="text-neutral-500 mt-2 text-base">{{ __('client.auth.register.form_subtitle') }}</p>
                </div>

                <form action="{{ route('client.register.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ $redirectTarget }}">

                    <!-- Name Input -->
                    <div class="group/input relative">
                        <label for="name" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">{{ __('client.auth.register.name_label') }}</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-brand-600 transition-colors">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input id="name" name="name" type="text"
                                class="kx-form-control w-full pl-11 pr-4 py-3.5 font-medium placeholder:text-neutral-400"
                                placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                        </div>
                        @error('name')
                            <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="group/input relative">
                        <label for="email" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Email</label>
                        <p class="text-xs text-neutral-500 mb-2 ml-1">{{ __('client.auth.register.email_hint') }}</p>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-brand-600 transition-colors">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input id="email" name="email" type="email"
                                class="kx-form-control w-full pl-11 pr-4 py-3.5 font-medium placeholder:text-neutral-400"
                                placeholder="email@example.com" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Input -->
                    <div class="group/input relative">
                        <label for="phone" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">{{ __('client.auth.register.phone_label') }}</label>
                        <p class="text-xs text-neutral-500 mb-2 ml-1">{{ __('client.auth.register.phone_hint') }}</p>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-brand-600 transition-colors">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input id="phone" name="phone" type="tel"
                                class="kx-form-control w-full pl-11 pr-4 py-3.5 font-medium placeholder:text-neutral-400"
                                placeholder="0912 345 678" value="{{ old('phone') }}">
                        </div>
                        @error('phone')
                            <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Password Input -->
                        <div class="group/input relative">
                            <label for="password" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">{{ __('client.auth.register.password_label') }}</label>
                            <p class="text-xs text-neutral-500 mb-2 ml-1">{{ __('client.auth.register.password_hint') }}</p>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-brand-600 transition-colors">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input id="password" name="password" type="password"
                                    class="kx-form-control w-full pl-11 pr-4 py-3.5 font-medium placeholder:text-neutral-400"
                                    placeholder="••••••••" required>
                            </div>
                            @error('password')
                                <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="group/input relative">
                            <label for="password_confirmation" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">{{ __('client.auth.register.password_confirmation_label') }}</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-brand-600 transition-colors">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    class="kx-form-control w-full pl-11 pr-4 py-3.5 font-medium placeholder:text-neutral-400"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="kx-btn-primary w-full py-4">
                        <span class="flex items-center justify-center gap-2">
                            <i class="fa-solid fa-user-plus text-sm"></i>
                            <span>{{ __('client.auth.register.submit') }}</span>
                        </span>
                    </button>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-neutral-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-neutral-500">{{ __('client.auth.common.or') }}</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-neutral-600">
                            {{ __('client.auth.register.has_account') }}
                            <a href="{{ route('client.login', ['redirect_to' => $redirectTarget]) }}"
                                class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                                {{ __('client.auth.register.login_now') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-client.layout>
