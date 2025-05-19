@extends("kingexpressbus.client.layouts.main")

@section("title", "Đăng ký tài khoản")

@pushonce('styles')
    <style>
        /* Thêm style tùy chỉnh nếu cần */
    </style>
@endpushonce

@section("content")
    <div class="bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 min-h-[calc(100vh-200px)]">
        <div class="max-w-md w-full space-y-8 bg-white p-8 md:p-10 rounded-xl shadow-lg border border-gray-200">
            <div>
                <img class="mx-auto h-12 w-auto"
                     src="{{ $webInfoGlobal->logo ?? 'https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg' }}"
                     alt="Logo">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-yellow-800">
                    Đăng ký tài khoản mới
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Hoặc
                    <a href="{{ route('client.login_page') }}"
                       class="font-medium text-yellow-600 hover:text-yellow-500">
                        đăng nhập nếu bạn đã có tài khoản
                    </a>
                </p>
            </div>

            {{-- Hiển thị lỗi đăng ký chung --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-5" action="{{ route('client.register') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    {{-- Fullname --}}
                    <div>
                        <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span
                                class="text-red-500">*</span></label>
                        <input id="fullname" name="fullname" type="text" autocomplete="name" required
                               value="{{ old('fullname') }}"
                               class="appearance-none relative block w-full px-3 py-2 border {{ $errors->has('fullname') ? 'border-red-500' : 'border-gray-300' }} placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                               placeholder="Nguyễn Văn A">
                        @error('fullname')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="email-address" class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ email
                            <span class="text-red-500">*</span></label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="appearance-none relative block w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                               placeholder="email@example.com">
                        @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span
                                class="text-red-500">*</span></label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required value="{{ old('phone') }}"
                               class="appearance-none relative block w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                               placeholder="09xxxxxxxx">
                        @error('phone')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span
                                class="text-red-500">*</span></label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="appearance-none relative block w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                               placeholder="Mật khẩu (ít nhất 8 ký tự)">
                        @error('password')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password-confirmation" class="block text-sm font-medium text-gray-700 mb-1">Xác nhận
                            mật khẩu <span class="text-red-500">*</span></label>
                        <input id="password-confirmation" name="password_confirmation" type="password"
                               autocomplete="new-password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                               placeholder="Nhập lại mật khẩu">
                        {{-- Không cần hiển thị lỗi riêng cho confirmation, lỗi 'confirmed' sẽ hiển thị ở trường password --}}
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
