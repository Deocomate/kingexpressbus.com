@props([
    'url',            // endpoint to toggle
    'value',          // current boolean value
    'label' => '',    // screen-reader label
    'method' => 'PATCH', // HTTP verb of the toggle endpoint
])
<div
    data-toggle-cell
    data-url="{{ $url }}"
    data-method="{{ $method }}"
    class="flex justify-center"
>
    <button
        type="button"
        aria-label="{{ $label }}"
        aria-pressed="{{ $value ? 'true' : 'false' }}"
        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-1
            {{ $value ? 'bg-brand-500' : 'bg-gray-200' }}"
    >
        <span
            aria-hidden="true"
            class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                {{ $value ? 'translate-x-4' : 'translate-x-0' }}"
        ></span>
    </button>
</div>
