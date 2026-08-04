@props([
    'id'       => 'data-table',
    'striped'  => false,
])
<div
    id="{{ $id }}"
    data-table
    class="overflow-x-auto rounded border border-gray-200 shadow-sm bg-white"
>
    {{ $slot }}
</div>
