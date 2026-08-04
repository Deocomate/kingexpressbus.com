@extends('admin.layouts.app')

@section('title', 'Phụ thu')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Phụ thu</span>
@endsection

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Phụ thu lễ tết</h1>
        <a href="{{ route('admin.surcharges.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-brand-600 hover:bg-brand-700 text-white rounded shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm phụ thu
        </a>
    </div>

    @include('admin.surcharges._table')
</div>
@endsection
