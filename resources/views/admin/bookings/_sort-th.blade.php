{{-- Sort-able table header cell.
     Variables: $key, $label, $sortable (bool), $sortKey, $sortDir, $activeTab, $search, $filters
--}}
@if($sortable)
    @php
        $isCurrent = $sortKey === $key;
        $nextDir   = ($isCurrent && $sortDir === 'asc') ? 'desc' : 'asc';
        $url = request()->fullUrlWithQuery(['sort' => $key, 'direction' => $nextDir, 'tab' => $activeTab, 'search' => $search, 'page' => 1] + collect($filters ?? [])->mapWithKeys(fn ($v, $k) => ["filter[$k]" => $v])->all());
    @endphp
    <a href="{{ $url }}" class="inline-flex items-center gap-0.5 hover:text-gray-900 transition-colors">
        {{ $label }}
        @if($isCurrent)
            <span class="text-brand-600">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
        @else
            <span class="text-gray-300">↕</span>
        @endif
    </a>
@else
    {{ $label }}
@endif
