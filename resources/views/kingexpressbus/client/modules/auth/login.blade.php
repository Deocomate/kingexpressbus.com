@extends("kingexpressbus.client.layouts.main")

@section("title", "Đăng nhập tài khoản")

@pushonce('styles')
    <style>
        /* Thêm style tùy chỉnh nếu cần */
    </style>
@endpushonce

@section("content")
    <div
        class="bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 min-h-[calc(100vh-200px)]"> {{-- Đảm bảo chiều cao tối thiểu --}}
        <div class="max-w-md w-full space-y-8 bg-white p-8 md:p-10 rounded-xl shadow-lg border border-gray-200">
            <div>
                <img class="mx-auto h-12 w-auto"
                     src="{{ $webInfoGlobal->logo ?? 'https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg' }}"
                     alt="Logo">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-yellow-800">
                    Đăng nhập tài khoản
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Hoặc
                    <a href="{{ route('client.register_page') }}"
                       class="font-medium text-yellow-600 hover:text-yellow-500">
                        đăng ký tài khoản mới
                    </a>
                </p>
            </div>

            {{-- Hiển thị lỗi đăng nhập chung --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            {{-- Hiển thị thông báo thành công (ví dụ: sau khi đăng ký) --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                     role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif


            <form class="mt-8 space-y-6" action="{{ route('client.login') }}" method="POST">
                @csrf
                <input type="hidden" name="remember" value="true">
                <div class="rounded-md shadow-sm -space-y-px">
                    {{-- Email --}}
                    <div>
                        <label for="email-address" class="sr-only">Địa chỉ email</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 focus:z-10 sm:text-sm"
                               placeholder="Địa chỉ email">
                        @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Password --}}
                    <div>
                        <label for="password" class="sr-only">Mật khẩu</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 focus:z-10 sm:text-sm"
                               placeholder="Mật khẩu">
                        @error('password')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-yellow-500 group-hover:text-yellow-400"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </span>
                        Đăng nhập
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
