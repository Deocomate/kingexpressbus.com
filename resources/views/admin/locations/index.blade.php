@extends('admin.layouts.app')

@section('title', 'Địa điểm')

@section('content')
<div>
    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Địa điểm</h1>
            <p class="text-sm text-gray-500 mt-0.5">Quản lý tỉnh/thành, loại địa điểm, địa điểm và điểm dừng</p>
        </div>
        @if($section === 'provinces')
            <a href="{{ route('admin.locations.provinces.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm tỉnh/thành
            </a>
        @elseif($section === 'district-types')
            <button type="button"
                    @click="$dispatch('open-slide-over', { id: 'dt-create-over' })"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm loại địa điểm
            </button>
        @elseif($section === 'districts')
            <a href="{{ route('admin.locations.districts.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm địa điểm
            </a>
        @elseif($section === 'stops')
            <button type="button"
                    @click="$dispatch('open-slide-over', { id: 'stop-create-over' })"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded hover:bg-brand-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm điểm dừng
            </button>
        @endif
    </div>

    {{-- Section tabs --}}
    @include('admin.locations._section-tabs', ['activeSection' => $section])

    {{-- Search bar --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.locations.index') }}" class="flex gap-2" data-table-filter>
            <input type="hidden" name="section" value="{{ $section }}">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Tìm kiếm…"
                class="block w-full max-w-xs rounded border border-gray-300 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
            >
            <button type="submit" class="px-3 py-2 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 text-gray-700 transition-colors">Tìm</button>
            @if(request('search'))
            <a href="{{ route('admin.locations.index', ['section' => $section]) }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Xóa</a>
            @endif
        </form>
    </div>

    {{-- Section content --}}
    @if($section === 'provinces')
        @include('admin.locations._table-provinces')
    @elseif($section === 'district-types')
        @include('admin.locations._table-district-types')
    @elseif($section === 'districts')
        @include('admin.locations._table-districts')
    @elseif($section === 'stops')
        @include('admin.locations._table-stops')
    @endif
</div>

{{-- Slide-overs (DistrictType + Stop) --}}
@if($section === 'district-types')
    @include('admin.locations._form-district-type')
@endif

@if($section === 'stops')
    @include('admin.locations._form-stop')
@endif
@endsection
