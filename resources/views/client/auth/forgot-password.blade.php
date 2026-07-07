<x-client.layout :title="__('client.auth.forgot_password.meta_title')"
    :description="__('client.auth.forgot_password.meta_description')">
    <section class="min-h-screen flex items-center justify-center bg-page py-12 px-4 sm:px-6 lg:px-8">
        <div class="kx-panel-strong w-full max-w-md p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-0.5 bg-brand-600"></div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-semibold text-neutral-800 tracking-tight">{{ __('client.auth.forgot_password.title') }}</h2>
                <p class="text-neutral-500 mt-2 text-sm">{{ __('client.auth.forgot_password.subtitle') }}</p>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-sm bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                    <i class="fa-solid fa-circle-check mr-1.5"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('client.password.email') }}" method="POST" class="space-y-6">
                @csrf

                <div class="group/input relative">
                    <label for="email" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-brand-600 transition-colors">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email"
                            class="kx-form-control w-full pl-11 pr-4 py-3.5 font-medium placeholder:text-neutral-400"
                            placeholder="email@example.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="kx-btn-primary w-full py-3.5">
                    {{ __('client.auth.forgot_password.submit') }}
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-neutral-600">
                <a href="{{ route('client.login') }}"
                    class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">{{ __('client.auth.common.back_to_login') }}</a>
            </div>
        </div>
    </section>
</x-client.layout>
