@extends('admin.layouts.app')

@section('title', 'Đội xe')
@section('breadcrumb')
    <span class="text-gray-700 font-medium">Đội xe</span>
@endsection

@section('content')
<div class="space-y-4">
    {{-- Section tabs --}}
    <div class="flex items-center gap-0 border-b border-gray-200">
        <a href="{{ route('admin.buses.index') }}?section=buses"
           class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
               {{ ($section ?? 'buses') === 'buses'
                    ? 'border-amber-500 text-amber-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Xe khách
        </a>
        <a href="{{ route('admin.buses.index') }}?section=services"
           class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
               {{ ($section ?? 'buses') === 'services'
                    ? 'border-amber-500 text-amber-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Dịch vụ xe
        </a>
    </div>

    @if(($section ?? 'buses') === 'buses')
        @include('admin.buses._table-buses')
    @else
        @include('admin.buses._table-services')
    @endif
</div>
@endsection
