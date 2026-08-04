@extends('admin.layouts.app')

@section('title', 'Cấu hình website')

@section('content')
<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Cấu hình website</h1>
            <p class="mt-1 text-sm text-gray-500">Quản lý hồ sơ website và menu điều hướng.</p>
        </div>
        @if($section === 'profile')
        <a href="{{ route('admin.website.profiles.create') }}"
           class="inline-flex items-center gap-1.5 rounded bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm hồ sơ
        </a>
        @endif
    </div>

    {{-- Section tabs --}}
    @include('admin.components.nav.section-tabs', [
        'tabs'          => ['profile' => 'Hồ sơ website', 'menus' => 'Menu điều hướng'],
        'activeSection' => $section,
    ])

    @if($section === 'profile')
        @include('admin.website._profiles-table', ['profiles' => $profiles])
    @elseif($section === 'menus')
        @include('admin.website.menu-tree')
    @endif
</div>
@endsection
