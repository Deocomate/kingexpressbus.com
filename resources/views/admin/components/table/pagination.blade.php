@props(['paginator'])
@if($paginator->hasPages())
<nav class="flex items-center justify-between px-4 py-3 border-t border-gray-200" aria-label="Pagination">
    <div class="text-sm text-gray-500">
        Hiển thị <span class="font-medium">{{ $paginator->firstItem() }}</span>–<span class="font-medium">{{ $paginator->lastItem() }}</span>
        trong <span class="font-medium">{{ $paginator->total() }}</span> bản ghi
    </div>
    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if($paginator->onFirstPage())
        <span class="px-2 py-1 text-sm text-gray-300 cursor-not-allowed rounded">‹ Trước</span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-2 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded transition-colors" data-table-page>‹ Trước</a>
        @endif

        {{-- Page numbers --}}
        @foreach($paginator->getUrlRange(max(1, $paginator->currentPage()-2), min($paginator->lastPage(), $paginator->currentPage()+2)) as $page => $url)
            @if($page == $paginator->currentPage())
            <span class="px-2.5 py-1 text-sm font-medium bg-brand-500 text-white rounded">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="px-2.5 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded transition-colors" data-table-page>{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-2 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded transition-colors" data-table-page>Tiếp ›</a>
        @else
        <span class="px-2 py-1 text-sm text-gray-300 cursor-not-allowed rounded">Tiếp ›</span>
        @endif
    </div>
</nav>
@endif
