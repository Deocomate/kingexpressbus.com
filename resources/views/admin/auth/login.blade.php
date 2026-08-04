@extends('admin.layouts.guest')

@section('title', 'Đăng nhập quản trị')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="flex justify-center mb-8">
            <div class="inline-flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-amber-500 text-white font-bold text-lg">K</span>
                <span class="text-xl font-bold text-gray-900">KingExpressBus</span>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8">
            <h1 class="text-lg font-semibold text-gray-900 mb-6 text-center">Đăng nhập quản trị</h1>

            @if($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" novalidate>
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        class="w-full px-3 py-2 border rounded text-sm transition-colors
                            {{ $errors->has('email') ? 'border-red-400 bg-red-50 focus:ring-red-400' : 'border-gray-300 bg-white focus:ring-amber-400' }}
                            focus:outline-none focus:ring-2 focus:ring-offset-0"
                        placeholder="admin@example.com"
                    >
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-3 py-2 border border-gray-300 rounded bg-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-0 transition-colors"
                        placeholder="••••••••"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-sm font-medium rounded transition-colors focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2"
                >
                    Đăng nhập
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-gray-400">
            Chỉ dành cho quản trị viên
        </p>
    </div>
</div>
@endsection
