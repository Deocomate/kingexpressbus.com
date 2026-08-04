@props([
    'name',
    'label'    => null,
    'value'    => null,
    'required' => false,
    'hint'     => null,
    'height'   => '320px',
])
@php
    $hasError = $errors->has($name);
    $inputId  = 'field-' . str_replace(['.', '[', ']'], ['-', '-', ''], $name);
    $val      = old($name, $value) ?? '';
    $uploadUrl = route('admin.api.upload.process');
@endphp
<div>
    @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
    @endif
    <div
        data-rich-text
        data-name="{{ $name }}"
        data-upload-url="{{ $uploadUrl }}"
        style="min-height: {{ $height }}"
        class="border rounded {{ $hasError ? 'border-red-400' : 'border-gray-300' }} bg-white"
        @if($hasError) aria-invalid="true" @endif
    ></div>
    {{-- Hidden textarea stores HTML value for form submission --}}
    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        class="sr-only"
        @if($required) required @endif
        aria-hidden="true"
    >{{ $val }}</textarea>
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$name" />
</div>
