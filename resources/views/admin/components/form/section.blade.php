@props(['title' => null, 'description' => null])
<div class="bg-white rounded border border-gray-200 shadow-sm">
    @if($title)
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-800">{{ $title }}</h3>
        @if($description)
        <p class="mt-0.5 text-xs text-gray-500">{{ $description }}</p>
        @endif
    </div>
    @endif
    <div class="px-6 py-5 space-y-5">
        {{ $slot }}
    </div>
</div>
