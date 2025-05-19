<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield("title", $webInfoGlobal->title ?? config('app.name'))</title> {{-- Thêm title mặc định --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    {{-- Favicon (Thêm link favicon của bạn) --}}
    {{-- <link rel="icon" href="/favicon.ico" type="image/x-icon"> --}}
    @stack("styles")
    {{-- Alpine JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Thêm style để đảm bảo body chiếm đủ chiều cao nếu footer cần sticky */
        /* html, body { height: 100%; }
        body { display: flex; flex-direction: column; }
        .main-content { flex-grow: 1; } */
        /* Tùy chọn: Thêm font chữ mặc định */
        /* body { font-family: 'Inter', sans-serif; } */
        /* @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); */
    </style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased"> {{-- Thêm màu nền và font smoothing --}}

{{-- *** KHU VỰC HIỂN THỊ ALERT TỪ SESSION (Đã di chuyển lên đây) *** --}}
<div class="alert-container"> {{-- Container để quản lý vị trí alert nếu cần --}}
    @if(session('success'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)" {{-- Tự động ẩn sau 5 giây --}}
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="relative w-full bg-green-500 text-white p-3 shadow-md" {{-- Thay đổi style --}}
             role="alert">
            <div class="container mx-auto px-4 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" type="button"
                        class="ml-4 text-green-100 hover:text-white focus:outline-none">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 7000)" {{-- Lỗi hiển thị lâu hơn --}}
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="relative w-full bg-red-500 text-white p-3 shadow-md" {{-- Thay đổi style --}}
             role="alert">
            <div class="container mx-auto px-4 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" type="button"
                        class="ml-4 text-red-100 hover:text-white focus:outline-none">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>
{{-- *** KẾT THÚC KHU VỰC ALERT *** --}}

{{-- Header --}}
@include("kingexpressbus.client.components.header")

{{-- Main Content --}}
<main class="main-content">
    @yield("content")
</main>

{{-- Footer --}}
@include("kingexpressbus.client.components.footer")

{{-- Scripts stack --}}
@stack("scripts")

</body>
</html>
