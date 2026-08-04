@props([
    'actions' => [],  // [['label' => ..., 'value' => ..., 'class' => ...]]
    'formAction' => '',
])
<div
    x-data="{ selected: [] }"
    x-show="selected.length > 0"
    x-cloak
    data-bulk-bar
    class="flex items-center gap-3 px-4 py-2 bg-brand-50 border-b border-brand-200"
>
    <span class="text-sm text-brand-700 font-medium" x-text="selected.length + ' đã chọn'"></span>
    <div class="flex gap-2 ml-auto">
        @foreach($actions as $action)
        <button
            type="button"
            data-bulk-action="{{ $action['value'] }}"
            data-form-action="{{ $formAction }}"
            class="px-3 py-1 text-xs rounded font-medium {{ $action['class'] ?? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }} transition-colors"
        >
            {{ $action['label'] }}
        </button>
        @endforeach
    </div>
</div>
