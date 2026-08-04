@props([
    'nameFrom' => 'date_from',
    'nameTo'   => 'date_to',
    'label'    => null,
    'valueFrom'=> null,
    'valueTo'  => null,
    'required' => false,
    'disabled' => false,
    'hint'     => null,
])
@php
    $fromId  = 'field-' . $nameFrom;
    $toId    = 'field-' . $nameTo;
    $fromVal = old($nameFrom, $valueFrom);
    $toVal   = old($nameTo, $valueTo);
    $hasError = $errors->has($nameFrom) || $errors->has($nameTo);
@endphp
<div>
    @if($label)
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    @endif
    <div class="flex items-center gap-2">
        <input
            type="text"
            id="{{ $fromId }}"
            name="{{ $nameFrom }}"
            value="{{ $fromVal }}"
            autocomplete="off"
            data-datepicker
            data-mode="date"
            placeholder="Từ ngày"
            @if($required) required @endif
            @if($disabled) disabled @endif
            class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
        >
        <span class="text-gray-400 text-sm shrink-0">→</span>
        <input
            type="text"
            id="{{ $toId }}"
            name="{{ $nameTo }}"
            value="{{ $toVal }}"
            autocomplete="off"
            data-datepicker
            data-mode="date"
            placeholder="Đến ngày"
            @if($required) required @endif
            @if($disabled) disabled @endif
            class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
        >
    </div>
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$nameFrom" />
    <x-admin::form.field-error :name="$nameTo" />
</div>
