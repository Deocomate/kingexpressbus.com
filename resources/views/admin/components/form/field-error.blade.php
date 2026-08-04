@props(['name'])
@error($name)
<p class="mt-1 text-xs text-red-600" id="{{ $name }}-error" role="alert">{{ $message }}</p>
@enderror
