@props([
    'name',
    'label'      => null,
    'options'    => [],   // static: ['value' => 'label']
    'value'      => null, // pre-selected value
    'valueText'  => null, // pre-selected label (for remote)
    'source'     => null, // remote source slug (uses OptionsController)
    'multiple'   => false,
    'required'   => false,
    'disabled'   => false,
    'placeholder'=> '— Tìm kiếm —',
    'hint'       => null,
])
@php
    $hasError = $errors->has($name);
    $inputId  = 'field-' . str_replace(['.', '[', ']'], ['-', '-', ''], $name);
    $selected = old($name, $value);
    $fieldName = $multiple ? $name . '[]' : $name;
@endphp
<div>
    @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
    @endif
    <select
        id="{{ $inputId }}"
        name="{{ $fieldName }}"
        @if($multiple) multiple @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
        data-select-search
        @if($source) data-source="{{ route('admin.api.options', $source) }}" @endif
        @if($valueText) data-value-text="{{ $valueText }}" @endif
        {{ $attributes->merge(['class' => 'block w-full text-sm']) }}
    >
        @if(!$source)
            @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected(is_array($selected) ? in_array((string)$optValue, array_map('strval', $selected)) : (string)$selected === (string)$optValue)>{{ $optLabel }}</option>
            @endforeach
        @elseif($selected)
            <option value="{{ $selected }}" selected>{{ $valueText ?? $selected }}</option>
        @endif
    </select>
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$name" />
</div>
