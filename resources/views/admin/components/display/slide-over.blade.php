@props([
    'id'    => 'slide-over',
    'title' => '',
    'width' => 'max-w-lg',
])
<div
    x-data="{ open: false }"
    x-on:open-slide-over.window="if ($event.detail.id === '{{ $id }}') open = true"
    x-on:close-slide-over.window="if ($event.detail.id === '{{ $id }}') open = false"
    id="{{ $id }}"
    class="relative z-40"
    aria-labelledby="{{ $id }}-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-black/30"
        x-cloak
    ></div>

    {{-- Panel --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex {{ $width }}">
                <div
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-full bg-white shadow-xl flex flex-col"
                    x-cloak
                >
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h2 id="{{ $id }}-title" class="text-base font-semibold text-gray-900">{{ $title }}</h2>
                        <button
                            type="button"
                            @click="open = false"
                            class="rounded p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                            aria-label="Đóng"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
