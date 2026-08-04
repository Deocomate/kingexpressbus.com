{{-- Admin topbar: breadcrumb left, user info + logout right --}}
<header class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200 flex-shrink-0">
    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-sm text-gray-500 min-w-0">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 transition-colors flex-shrink-0">Trang chủ</a>
        @hasSection('breadcrumb')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0 text-gray-400" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
            @yield('breadcrumb')
        @endif
    </nav>

    <div class="flex items-center gap-4 flex-shrink-0">
        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?: auth()->user()->email }}</span>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button
                type="submit"
                class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-red-600 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                Đăng xuất
            </button>
        </form>
    </div>
</header>
