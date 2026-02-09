<x-client.layout title="Quên mật khẩu"
    description="Nhập email để nhận liên kết đặt lại mật khẩu cho tài khoản King Express Bus.">
    <section class="min-h-screen flex items-center justify-center bg-neutral-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md bg-white rounded-lg shadow-card p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-0.5 bg-primary-600"></div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-semibold text-neutral-800 tracking-tight">Quên mật khẩu</h2>
                <p class="text-neutral-500 mt-2 text-sm">Chúng tôi sẽ gửi liên kết đặt lại mật khẩu đến email của bạn.</p>
            </div>

            @if (session('status'))
                <div class="mb-6 rounded-md bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                    <i class="fa-solid fa-circle-check mr-1.5"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('client.password.email') }}" method="POST" class="space-y-6">
                @csrf

                <div class="group/input relative">
                    <label for="email" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email"
                            class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                            placeholder="email@example.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-md transition-colors duration-200">
                    Gửi liên kết đặt lại
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-neutral-600">
                <a href="{{ route('client.login') }}"
                    class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">Quay lại đăng nhập</a>
            </div>
        </div>
    </section>
</x-client.layout>
