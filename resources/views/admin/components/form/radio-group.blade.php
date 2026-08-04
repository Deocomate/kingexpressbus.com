@props([
    'name',
    'label'    => null,
    'options'  => [],
    'value'    => null,
    'required' => false,
    'disabled' => false,
    'inline'   => false,
    'hint'     => null,
])
@php
    $selected = old($name, $value);
    $hasError = $errors->has($name);
@endphp
<fieldset>
    @if($label)
    <legend class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </legend>
    @endif
    <div class="{{ $inline ? 'flex flex-wrap gap-4' : 'space-y-2' }}">
        @foreach($options as $optValue => $optLabel)
        @php $radioId = 'radio-' . $name . '-' . $optValue; @endphp
        <div class="flex items-center gap-2">
            <input
                type="radio"
                id="{{ $radioId }}"
                name="{{ $name }}"
                value="{{ $optValue }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($hasError) aria-invalid="true" @endif
                @checked((string)$selected === (string)$optValue)
                class="h-4 w-4 text-brand-500 border-gray-300 focus:ring-brand-500 disabled:opacity-50"
            >
            <label for="{{ $radioId }}" class="text-sm text-gray-700 cursor-pointer">{{ $optLabel }}</label>
        </div>
        @endforeach
    </div>
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$name" />
</fieldset>
