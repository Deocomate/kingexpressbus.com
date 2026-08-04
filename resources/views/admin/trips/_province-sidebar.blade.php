@php
    $totalRoutes = $provinceStats->sum('route_count');
    $totalTrips  = $provinceStats->sum('trip_count');
    $selectedId  = $selectedProvince?->id;
@endphp
<aside class="w-full lg:w-60 shrink-0">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tỉnh thành xuất phát</h2>
        </div>
        <nav class="max-h-[70vh] overflow-y-auto divide-y divide-gray-50">
            <a href="{{ route('admin.trips.index', ['section' => 'trips']) }}"
               class="flex items-center justify-between gap-2 px-4 py-2.5 text-sm transition-colors
                      {{ is_null($selectedId) ? 'bg-brand-50 text-brand-700 font-medium border-l-2 border-brand-600' : 'text-gray-700 hover:bg-gray-50 border-l-2 border-transparent' }}">
                <span>Tất cả tỉnh</span>
                <span class="text-xs tabular-nums {{ is_null($selectedId) ? 'text-brand-500' : 'text-gray-400' }}">{{ $totalTrips }}</span>
            </a>
            @forelse($provinceStats as $p)
            <a href="{{ route('admin.trips.index', ['section' => 'trips', 'filter' => ['province_id' => $p->id]]) }}"
               class="flex items-center justify-between gap-2 px-4 py-2.5 text-sm transition-colors
                      {{ $selectedId === $p->id ? 'bg-brand-50 text-brand-700 font-medium border-l-2 border-brand-600' : 'text-gray-700 hover:bg-gray-50 border-l-2 border-transparent' }}">
                <span class="truncate">{{ $p->name }}</span>
                <span class="flex items-center gap-1.5 shrink-0">
                    <span class="text-[11px] text-gray-400">{{ $p->route_count }} tuyến</span>
                    <span class="text-xs tabular-nums {{ $selectedId === $p->id ? 'text-brand-500' : 'text-gray-400' }}">{{ $p->trip_count }}</span>
                </span>
            </a>
            @empty
            <p class="px-4 py-6 text-xs text-gray-400 text-center">Chưa có tuyến đường nào.</p>
            @endforelse
        </nav>
    </div>
</aside>
