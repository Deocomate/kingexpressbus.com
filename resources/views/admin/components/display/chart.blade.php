@props([
    'id'      => null,
    'type'    => 'line',   // line|bar|doughnut|pie
    'data'    => [],        // Chart.js data config (PHP array, will be JSON encoded)
    'options' => [],
    'height'  => '300px',
])
@php
    $chartId = $id ?? 'chart-' . uniqid();
@endphp
<div
    data-chart
    data-chart-id="{{ $chartId }}"
    data-chart-type="{{ $type }}"
    data-chart-data="{{ json_encode($data) }}"
    data-chart-options="{{ json_encode($options) }}"
    style="height: {{ $height }}"
    class="relative"
>
    <canvas id="{{ $chartId }}"></canvas>
</div>
