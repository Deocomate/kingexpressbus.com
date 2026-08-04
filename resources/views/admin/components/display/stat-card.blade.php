@props([
    'title',
    'value',
    'icon'       => null,
    'change'     => null,
    'changeType' => 'neutral', // up|down|neutral
    'description'=> null,
    'color'      => 'primary',
])
@php
    $colorMap = [
        'primary'   => 'text-brand-600 bg-brand-50',
        'success'   => 'text-green-600 bg-green-50',
        'danger'    => 'text-red-600 bg-red-50',
        'warning'   => 'text-yellow-600 bg-yellow-50',
        'info'      => 'text-blue-600 bg-blue-50',
    ];
    $iconClasses = $colorMap[$color] ?? $colorMap['primary'];
@endphp
<div class="bg-white rounded border border-gray-200 shadow-sm p-5">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $title }}</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-900">{{ $value }}</p>
            @if($description)
            <p class="mt-0.5 text-xs text-gray-500">{{ $description }}</p>
            @endif
            @if($change !== null)
            <p class="mt-1 text-xs font-medium
                {{ $changeType === 'up' ? 'text-green-600' : ($changeType === 'down' ? 'text-red-600' : 'text-gray-500') }}">
                {{ $changeType === 'up' ? '↑' : ($changeType === 'down' ? '↓' : '') }} {{ $change }}
            </p>
            @endif
        </div>
        @if($icon)
        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $iconClasses }} flex items-center justify-center text-xl">
            {{ $icon }}
        </div>
        @endif
    </div>
</div>
