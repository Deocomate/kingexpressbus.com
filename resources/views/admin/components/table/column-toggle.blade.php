@props([
    'columns' => [],  // array of TableColumn
    'tableId' => 'data-table',
])
@php
    $hideableColumns = array_filter($columns, fn($c) => is_object($c) ? $c->hideable : true);
@endphp
@if(!empty($hideableColumns))
<div
    x-data="{ open: false }"
    class="relative"
    data-column-toggle
    data-table-id="{{ $tableId }}"
>
    <button
        type="button"
        @click="open = !open"
        class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50 transition-colors"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        Cột
    </button>
    <div
        x-show="open"
        @click.outside="open = false"
        x-cloak
        class="absolute right-0 z-20 mt-1 w-48 bg-white rounded border border-gray-200 shadow-lg py-1"
    >
        @foreach($hideableColumns as $column)
        @php
            $key = is_object($column) ? $column->key : $column;
            $label = is_object($column) ? $column->label : $column;
            $defaultHidden = is_object($column) ? $column->defaultHidden : false;
        @endphp
        <label class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
            <input
                type="checkbox"
                @if(!$defaultHidden) checked @endif
                data-column-key="{{ $key }}"
                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500"
            >
            {{ $label }}
        </label>
        @endforeach
    </div>
</div>
@endif
