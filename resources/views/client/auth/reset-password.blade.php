<x-client.layout :title="__('client.auth.reset_password.meta_title')"
    :description="__('client.auth.reset_password.meta_description')">
    <section class="min-h-screen flex items-center justify-center bg-neutral-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md bg-white rounded-lg shadow-card p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-0.5 bg-primary-600"></div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-semibold text-neutral-800 tracking-tight">{{ __('client.auth.reset_password.title') }}</h2>
                <p class="text-neutral-500 mt-2 text-sm">{{ __('client.auth.reset_password.subtitle') }}</p>
            </div>

            <form action="{{ route('client.password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="group/input relative">
                    <label for="email" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email"
                            class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                            placeholder="email@example.com" value="{{ old('email', $email) }}" required autofocus>
                    </div>
                    @error('email')
                        <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="group/input relative">
                    <label for="password" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">{{ __('client.auth.reset_password.password_label') }}</label>
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

                <div class="group/input relative">
                    <label for="password_confirmation" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">{{ __('client.auth.reset_password.password_confirmation_label') }}</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-md transition-colors duration-200">
                    {{ __('client.auth.reset_password.submit') }}
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-neutral-600">
                <a href="{{ route('client.login') }}"
                    class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">{{ __('client.auth.common.back_to_login') }}</a>
            </div>
        </div>
    </section>
</x-client.layout>
