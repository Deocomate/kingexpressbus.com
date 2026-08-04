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
    // Raw stored paths (not the resolved display URL) — this is what the backend
    // (BusController/RouteController/etc. prepareData()) expects back for files
    // that are kept unchanged: a value without '~' is stored as-is, so it must
    // match the DB column exactly or the "unchanged" image gets treated as removed.
    $existingPaths = $multiple ? array_values(array_filter($existing)) : ($existing ? [$existing] : []);
    $existingUrls = array_map(fn ($path) => \App\Helpers\SystemHelper::mediaUrl($path), $existingPaths);
@endphp
<div>
    @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
    </label>
    @endif
    <div
        data-dropzone
        class="dropzone"
        data-name="{{ $name }}"
        data-multiple="{{ $multiple ? 'true' : 'false' }}"
        data-accept="{{ $accept }}"
        data-max-size="{{ $maxSize }}"
        data-process-url="{{ route('admin.api.upload.process') }}"
        data-revert-url="{{ route('admin.api.upload.revert') }}"
        @if(!empty($existingUrls))
            data-existing='@json($existingUrls)'
            data-existing-paths='@json($existingPaths)'
        @endif
    >
        {{-- Dropzone mounts here; class="dropzone" is required for CDN CSS
             (.dropzone .dz-preview …) and for Dropzone to inject dz-message. --}}
    </div>
    @if($hint && !$hasError)
    <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    <x-admin::form.field-error :name="$name" />
</div>
