@props([
    'key',
    'label',
    'colspan' => 1,
    'count'   => null,
])
@php
    $storageKey = 'admin_group_' . $key . '_collapsed';
@endphp
<tr
    data-group-header="{{ $key }}"
    x-data="{
        collapsed: localStorage.getItem('{{ $storageKey }}') === 'true',
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('{{ $storageKey }}', this.collapsed);
            document.querySelectorAll('[data-group-rows=\'{{ $key }}\']').forEach(tr => {
                tr.style.display = this.collapsed ? 'none' : '';
            });
        }
    }"
    x-init="if(collapsed) document.querySelectorAll('[data-group-rows=\'{{ $key }}\']').forEach(tr => tr.style.display = 'none')"
    class="bg-gray-50 cursor-pointer select-none hover:bg-gray-100 transition-colors"
    @click="toggle()"
>
    <td colspan="{{ $colspan }}" class="px-4 py-2 text-xs font-semibold text-gray-600 uppercase tracking-wide">
        <span class="flex items-center gap-2">
            <svg
                class="w-3 h-3 transition-transform"
                :class="{ '-rotate-90': collapsed }"
                fill="currentColor" viewBox="0 0 20 20"
            >
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
            {{ $label }}
            @if($count !== null)
            <span class="ml-1 text-gray-400">({{ $count }})</span>
            @endif
        </span>
    </td>
</tr>
