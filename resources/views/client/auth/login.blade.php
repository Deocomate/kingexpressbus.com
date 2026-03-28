<x-client.layout :title="__('client.auth.login.meta_title')"
    :description="__('client.auth.login.meta_description')">
    @php
        $redirectTarget = $redirectTo ?? route('client.profile.index');
    @endphp

    <section class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute inset-0 bg-slate-900 pointer-events-none z-0">
            <div class="absolute inset-0 opacity-20"
                style="background-image: url('/userfiles/files/kingexpressbus/cabin/1.jpg'); background-size: cover; background-position: center;">
            </div>
            <div class="absolute inset-0 bg-slate-900/60"></div>
        </div>

        <!-- Main Container -->
        <div class="relative z-10 w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">

            <!-- Left Side: Welcome Info (Hidden on Mobile) -->
            <div class="hidden lg:block text-white space-y-8 pr-10">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-accent-500 text-white uppercase tracking-wide">
                        <i class="fa-solid fa-star mr-2"></i> {{ __('client.auth.login.badge') }}
                    </span>
                    <h1 class="mt-6 text-5xl font-semibold leading-tight tracking-tight">
                        {{ __('client.auth.login.welcome_title') }} <br>
                        <span class="text-accent-500">{{ __('client.auth.login.welcome_brand') }}</span>
                    </h1>
                    <p class="mt-4 text-lg text-slate-300">
                        {{ __('client.auth.login.welcome_description') }}
                    </p>
                </div>

                <div class="space-y-6 mt-10">
                    <div class="flex items-start gap-4 p-4 rounded-lg bg-white/10">
                        <div class="shrink-0 w-12 h-12 rounded-md bg-primary-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">{{ __('client.auth.login.feature_manage_title') }}</h3>
                            <p class="text-sm text-slate-400 mt-1">{{ __('client.auth.login.feature_manage_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="bg-white rounded-lg shadow-card p-8 md:p-10 w-full max-w-md mx-auto lg:ml-auto relative overflow-hidden">
                <!-- Decorative top border -->
                <div class="absolute top-0 left-0 w-full h-0.5 bg-primary-600"></div>

                <div class="text-center mb-8">
                    <h2 class="text-3xl font-semibold text-neutral-800 tracking-tight">{{ __('client.auth.login.form_title') }}</h2>
                    <p class="text-neutral-500 mt-2 text-base">{{ __('client.auth.login.form_subtitle') }}</p>
                </div>

                <form action="{{ route('client.login.submit') }}" method="POST" class="space-y-7">
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ $redirectTarget }}">

                    <div class="space-y-5">
                        <!-- Login Input -->
                        <div class="group/input relative">
                            <label for="login" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">{{ __('client.auth.login.login_label') }}</label>
                            <p class="text-xs text-neutral-500 mb-2 ml-1">{{ __('client.auth.login.login_hint') }}</p>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input id="login" name="login" type="text"
                                    class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                                    placeholder="example@gmail.com" value="{{ old('login') }}" required autofocus>
                            </div>
                            @error('login')
                                <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="group/input relative">
                            <div class="flex justify-between items-center mb-1.5 ml-1">
                                <label for="password" class="block text-sm font-semibold text-neutral-700">{{ __('client.auth.login.password_label') }}</label>
                            </div>
                            <p class="text-xs text-neutral-500 mb-2 ml-1">{{ __('client.auth.login.password_hint') }}</p>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input id="password" name="password" type="password"
                                    class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                                    placeholder="••••••••" required>
                            </div>
                            @error('password')
                                <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2.5 cursor-pointer group/check">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="remember" value="1" class="peer sr-only">
                                <div class="w-5 h-5 border-2 border-neutral-300 rounded peer-checked:bg-primary-600 peer-checked:border-primary-600 transition-colors"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-neutral-600">{{ __('client.auth.login.remember') }}</span>
                        </label>
                    </div>

                    <div class="rounded-md border border-neutral-200 bg-neutral-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-md bg-white text-primary-600 flex items-center justify-center shadow-soft">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-neutral-800">{{ __('client.auth.login.forgot_password_title') }}</p>
                                <p class="text-xs text-neutral-500 mt-1">{{ __('client.auth.login.forgot_password_desc') }}</p>
                                <a href="{{ route('client.password.request') }}"
                                    class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                    <span>{{ __('client.auth.login.forgot_password_link') }}</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-4 rounded-md transition-colors duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span class="flex items-center justify-center gap-2">
                            <span>{{ __('client.auth.login.submit') }}</span>
                            <i class="fa-solid fa-arrow-right"></i>
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
                            {{ __('client.auth.login.no_account') }}
                            <a href="{{ route('client.register', ['redirect_to' => $redirectTarget]) }}"
                                class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                {{ __('client.auth.login.register_now') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-client.layout>
