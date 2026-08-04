@props([
    'name',
    'label'   => null,
    'value'   => false,
    'required'=> false,
    'disabled'=> false,
    'hint'    => null,
])
@php
    $checked  = (bool) old($name, $value);
    $inputId  = 'field-' . str_replace(['.', '[', ']'], ['-', '-', ''], $name);
    $hasError = $errors->has($name);
@endphp
<div class="flex items-start gap-3">
    <div class="flex items-center h-5 pt-0.5">
        <input type="hidden" name="{{ $name }}" value="0">
        <input
            type="checkbox"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="1"
            @if($checked) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            class="sr-only peer"
        >
        <label for="{{ $inputId }}"
            class="relative flex-none w-10 h-6 bg-gray-200 rounded-full cursor-pointer peer-checked:bg-brand-500 peer-disabled:cursor-not-allowed peer-disabled:opacity-50 transition-colors
                   after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-transform peer-checked:after:translate-x-4">
        </label>
    </div>
    @if($label)
    <div>
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 cursor-pointer">{{ $label }}</label>
        @if($hint && !$hasError)
        <p class="text-xs text-gray-500">{{ $hint }}</p>
        @endif
        <x-admin::form.field-error :name="$name" />
    </div>
    @endif
</div>
