@props([
    'name',
    'label'    => null,
    'value'    => null,
    'required' => false,
    'disabled' => false,
    'rows'     => 4,
    'hint'     => null,
    'placeholder' => '',
])
@php
    $hasError = $errors->has($name);
    $inputId  = 'field-' . str_replace(['.', '[', ']'], ['-', '-', ''], $name);
    $val      = old($name, $value);
@endphp
<div>
    @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
    @endif
    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded border ' . ($hasError ? 'border-red-400 bg-red-50 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-brand-500 focus:border-brand-500') . ' py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 disabled:bg-gray-50 disabled:text-gray-400 resize-y']) }}
    >{{ $val }}</textarea>
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$name" />
</div>
