@php
    $isActive  = $activeSortKey === $key;
    $nextDir   = ($isActive && $activeSortDir === 'asc') ? 'desc' : 'asc';
    $url       = request()->fullUrlWithQuery(['sort' => $key, 'direction' => $nextDir]);
@endphp
<a href="{{ $url }}" class="inline-flex items-center gap-1 hover:text-gray-800 transition-colors {{ $isActive ? 'text-gray-800' : '' }}">
    {{ $label }}
    @if($isActive)
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
            @if($activeSortDir === 'asc')
            <path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04L10.75 5.612V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd"/>
            @else
            <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" clip-rule="evenodd"/>
            @endif
        </svg>
    @endif
</a>
