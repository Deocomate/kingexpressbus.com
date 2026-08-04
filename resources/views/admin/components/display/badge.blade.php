@props([
    'color' => 'secondary',  // success|danger|warning|info|primary|secondary
    'size'  => 'sm',         // sm|xs
])
@php
    $colorMap = [
        'success'   => 'bg-green-100 text-green-800',
        'danger'    => 'bg-red-100 text-red-800',
        'warning'   => 'bg-yellow-100 text-yellow-800',
        'info'      => 'bg-blue-100 text-blue-800',
        'primary'   => 'bg-brand-100 text-brand-800',
        'secondary' => 'bg-gray-100 text-gray-700',
    ];
    $classes = $colorMap[$color] ?? $colorMap['secondary'];
    $sizeClass = $size === 'xs' ? 'text-xs px-1.5 py-0.5' : 'text-xs px-2 py-1';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full $classes $sizeClass"]) }}>
    {{ $slot }}
</span>
