@props([
    'name',
    'label'    => null,
    'min'      => 0,
    'max'      => 100,
    'step'     => 1,
    'value'    => null,    // single value OR [from, to] for range
    'connect'  => true,
    'hint'     => null,
])
@php
    $hasError = $errors->has($name);
    $inputId  = 'slider-' . str_replace(['.', '[', ']'], ['-', '-', ''], $name);
    $val      = old($name, $value);
    if (is_array($val)) {
        $valFrom = $val[0] ?? $min;
        $valTo   = $val[1] ?? $max;
    } else {
        $valFrom = $val ?? $min;
        $valTo   = null;
    }
    $isRange = ($valTo !== null);
@endphp
<div>
    @if($label)
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    @endif
    <div
        id="{{ $inputId }}"
        data-range-slider
        data-min="{{ $min }}"
        data-max="{{ $max }}"
        data-step="{{ $step }}"
        data-from="{{ $valFrom }}"
        @if($isRange) data-to="{{ $valTo }}" @endif
        data-name="{{ $name }}"
        class="mt-2 mb-4"
    ></div>
    <input type="hidden" name="{{ $name }}" value="{{ $val }}">
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$name" />
</div>
