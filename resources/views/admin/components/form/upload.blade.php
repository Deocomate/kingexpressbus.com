@props([
    'name',
    'label'      => null,
    'value'      => null,   // existing file path(s)
    'multiple'   => false,
    'required'   => false,
    'hint'       => null,
    'accept'     => 'image/*',
    'maxSize'    => 8388608, // 8MB in bytes
])
@php
    $hasError = $errors->has($name);
    $inputId  = 'field-' . str_replace(['.', '[', ']'], ['-', '-', ''], $name);
    $existing = old($name, $value);
    if ($multiple && !is_array($existing)) {
        $existing = $existing ? [$existing] : [];
    }
@endphp
<div>
    @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
    @endif
    <div
        data-filepond
        data-name="{{ $name }}"
        data-multiple="{{ $multiple ? 'true' : 'false' }}"
        data-accept="{{ $accept }}"
        data-max-size="{{ $maxSize }}"
        data-process-url="{{ route('admin.api.upload.process') }}"
        data-revert-url="{{ route('admin.api.upload.revert') }}"
        @if($multiple && is_array($existing))
            data-existing='@json(array_filter($existing))'
        @elseif(!$multiple && $existing)
            data-existing='@json([$existing])'
        @endif
    >
        {{-- FilePond mounts here --}}
    </div>
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$name" />
</div>
