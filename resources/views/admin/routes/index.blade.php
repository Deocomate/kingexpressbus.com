@extends('admin.layouts.app')

@section('title', 'Tuyến đường')
@section('breadcrumb')
    <span class="text-gray-700 font-medium">Tuyến đường</span>
@endsection

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Tuyến đường</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $paginator->total() }} tuyến</p>
        </div>
        <a href="{{ route('admin.routes.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-md transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Thêm tuyến
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.routes.index') }}" class="flex flex-wrap items-end gap-3 bg-white border border-gray-200 rounded p-3" data-table-filter>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input
                type="text"
                name="search"
                value="{{ $activeSearch }}"
                placeholder="Tên tuyến, tỉnh..."
                class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2.5 focus:outline-none focus:ring-1 focus:ring-amber-500"
            >
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tỉnh đầu</label>
            <select name="filter_province_start" class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <option value="">— Tất cả —</option>
                @foreach($provinces as $p)
                    <option value="{{ $p->id }}" @selected($filterProvinceStart == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tỉnh cuối</label>
            <select name="filter_province_end" class="block w-full text-sm rounded border border-gray-300 py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <option value="">— Tất cả —</option>
                @foreach($provinces as $p)
                    <option value="{{ $p->id }}" @selected($filterProvinceEnd == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded border border-gray-300 transition-colors">Lọc</button>
        @if($activeSearch || $filterProvinceStart || $filterProvinceEnd)
        <a href="{{ route('admin.routes.index') }}" class="px-4 py-1.5 text-sm text-gray-500 hover:text-gray-700 border border-transparent rounded">Xóa lọc</a>
        @endif
    </form>

    {{-- Bulk delete form --}}
    <form id="bulk-form-routes" method="POST" action="{{ route('admin.routes.bulk-destroy') }}">
        @csrf
        <x-admin::table.bulk-bar
            :actions="[['label' => 'Xóa đã chọn', 'value' => 'delete', 'class' => 'bg-red-50 border border-red-300 text-red-700 hover:bg-red-100']]"
            formAction="{{ route('admin.routes.bulk-destroy') }}"
        />

        {{-- Table --}}
        <x-admin::table.table>
            @include('admin.routes._table-rows', compact('paginator', 'activeSortKey', 'activeSortDir'))
        </x-admin::table.table>
    </form>
</div>
@endsection
