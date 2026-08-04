@extends('admin.layouts.app')

@section('title', 'Chuyến xe')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Chuyến xe</span>
@endsection

@section('content')
<div class="space-y-4">
    {{-- Page header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Chuyến xe</h1>
        @if($section === 'trips')
        <a href="{{ route('admin.trips.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-brand-600 hover:bg-brand-700 text-white rounded shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm chuyến
        </a>
        @else
        <a href="{{ route('admin.trips.blocks.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-brand-600 hover:bg-brand-700 text-white rounded shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm khóa chuyến
        </a>
        @endif
    </div>

    {{-- Section tabs --}}
    @include('admin.trips._section-tabs', ['activeSection' => $section])

    @if($section === 'trips')
        <div class="flex flex-col lg:flex-row gap-4 items-start">
            @include('admin.trips._province-sidebar', compact('provinceStats','selectedProvince'))

            <div class="flex-1 min-w-0 w-full">
                @if($selectedProvince)
                <div class="mb-3 flex items-center gap-2 text-sm text-gray-600">
                    <span>Tuyến đi từ</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-brand-50 text-brand-700 font-medium">
                        {{ $selectedProvince->name }}
                        <a href="{{ route('admin.trips.index', ['section' => 'trips']) }}" class="ml-1 text-brand-400 hover:text-brand-700" title="Bỏ lọc">&times;</a>
                    </span>
                </div>
                @endif
                @include('admin.trips._table', compact('paginator','grouped','activeTab','tabBadges','tabs','routes','buses','filterRouteId','filterBusId','search','selectedProvince','perPage','perPageOptions'))
            </div>
        </div>
    @else
        @include('admin.trips._table-blocks', compact('paginator','routes','filterRouteId','search','perPage','perPageOptions'))
    @endif
</div>
@endsection
