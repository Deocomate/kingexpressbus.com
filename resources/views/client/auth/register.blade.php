<x-client.layout title="Đăng ký tài khoản" description="Tạo tài khoản King Express Bus để trải nghiệm dịch vụ tốt hơn.">
    @php
        $redirectTarget = $redirectTo ?? route('client.profile.index');
    @endphp

    <section class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute inset-0 bg-slate-900 pointer-events-none z-0">
            <div class="absolute inset-0 opacity-20"
                style="background-image: url('/userfiles/files/kingexpressbus/cabin/3.jpg'); background-size: cover; background-position: center;">
            </div>
            <div class="absolute inset-0 bg-slate-900/60"></div>
        </div>

        <!-- Main Container -->
        <div class="relative z-10 w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">

            <!-- Left Side: Welcome Info (Hidden on Mobile) -->
            <div class="hidden lg:block text-white space-y-8 pr-10">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold bg-primary-600 text-white uppercase tracking-wide">
                        <i class="fa-solid fa-gift mr-2"></i> Đăng ký thành viên
                    </span>
                    <h1 class="mt-6 text-5xl font-semibold leading-tight tracking-tight">
                        Bắt đầu hành trình <br>
                        <span class="text-accent-500">cùng King Express</span>
                    </h1>
                    <p class="mt-4 text-lg text-slate-300">
                        Tạo tài khoản ngay hôm nay để tích điểm, đổi vé và nhận vô vàn ưu đãi hấp dẫn trên mọi chuyến đi.
                    </p>
                </div>

                <div class="grid gap-5 mt-10">
                    <div class="flex items-start gap-4 p-4 rounded-lg bg-white/10">
                        <div class="flex-shrink-0 w-12 h-12 rounded-md bg-accent-500 flex items-center justify-center text-white">
                            <i class="fa-solid fa-coins text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Tích điểm đổi quà</h3>
                            <p class="text-sm text-slate-400 mt-1">Mỗi chuyến đi đều mang lại điểm thưởng để quy đổi thành vé miễn phí.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-lg bg-white/10">
                        <div class="flex-shrink-0 w-12 h-12 rounded-md bg-primary-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-tags text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">Ưu đãi độc quyền</h3>
                            <p class="text-sm text-slate-400 mt-1">Nhận thông báo sớm nhất về các chương trình khuyến mãi và giảm giá sâu.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Register Form -->
            <div class="bg-white rounded-lg shadow-card p-8 md:p-10 w-full max-w-lg mx-auto lg:ml-auto relative overflow-hidden">
                <!-- Decorative top border -->
                <div class="absolute top-0 left-0 w-full h-0.5 bg-accent-500"></div>

                <div class="text-center mb-8">
                    <h2 class="text-3xl font-semibold text-neutral-800 tracking-tight">Tạo tài khoản</h2>
                    <p class="text-neutral-500 mt-2 text-base">Điền thông tin của bạn để đăng ký thành viên</p>
                </div>

                <form action="{{ route('client.register.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ $redirectTarget }}">

                    <!-- Name Input -->
                    <div class="group/input relative">
                        <label for="name" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Họ và tên</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input id="name" name="name" type="text"
                                class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                                placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                        </div>
                        @error('name')
                            <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="group/input relative">
                        <label for="email" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Email</label>
                        <p class="text-xs text-neutral-500 mb-2 ml-1">Liên kết xác nhận và thông báo đặt vé sẽ gửi về đây.</p>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input id="email" name="email" type="email"
                                class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                                placeholder="email@example.com" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Input -->
                    <div class="group/input relative">
                        <label for="phone" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Số điện thoại</label>
                        <p class="text-xs text-neutral-500 mb-2 ml-1">Tuỳ chọn, giúp nhận thông báo nhanh hơn.</p>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input id="phone" name="phone" type="tel"
                                class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                                placeholder="0912 345 678" value="{{ old('phone') }}">
                        </div>
                        @error('phone')
                            <p class="text-sm text-red-500 mt-1.5 ml-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Password Input -->
                        <div class="group/input relative">
                            <label for="password" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Mật khẩu</label>
                            <p class="text-xs text-neutral-500 mb-2 ml-1">Tối thiểu 8 ký tự.</p>
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

                        <!-- Confirm Password Input -->
                        <div class="group/input relative">
                            <label for="password_confirmation" class="block text-sm font-semibold text-neutral-700 mb-1.5 ml-1">Xác nhận</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 group-focus-within/input:text-primary-600 transition-colors">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    class="w-full pl-11 pr-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-md focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors font-medium text-neutral-800 placeholder:text-neutral-400"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-4 rounded-md transition-colors duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span class="flex items-center justify-center gap-2">
                            <i class="fa-solid fa-user-plus text-sm"></i>
                            <span>Đăng ký tài khoản</span>
                        </span>
                    </button>

                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-neutral-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-neutral-500">Hoặc</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-neutral-600">
                            Đã là thành viên?
                            <a href="{{ route('client.login', ['redirect_to' => $redirectTarget]) }}"
                                class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                Đăng nhập ngay
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-client.layout>
