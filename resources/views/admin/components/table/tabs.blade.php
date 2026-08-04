@props([
    'tabs'      => [],   // array of TableTab instances or ['key' => 'label']
    'activeTab' => '',
    'badges'    => [],   // ['key' => count|null]
])
<div class="border-b border-gray-200 bg-white px-4">
    <nav class="-mb-px flex gap-1" aria-label="Tabs" role="tablist">
        @foreach($tabs as $tab)
            @php
                $key    = is_array($tab) ? $tab['key'] : $tab->key;
                $label  = is_array($tab) ? $tab['label'] : $tab->label;
                $count  = $badges[$key] ?? null;
                $isActive = $activeTab === $key;
            @endphp
            <a
                href="{{ request()->fullUrlWithQuery(['tab' => $key, 'page' => 1]) }}"
                role="tab"
                aria-selected="{{ $isActive ? 'true' : 'false' }}"
                data-tab-key="{{ $key }}"
                class="group inline-flex items-center gap-1.5 px-3 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors
                    {{ $isActive
                        ? 'border-brand-500 text-brand-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                {{ $label }}
                @if($count !== null)
                <span class="inline-flex items-center justify-center rounded-full px-1.5 py-0.5 text-xs font-semibold min-w-[1.25rem]
                    {{ $isActive ? 'bg-brand-100 text-brand-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $count }}
                </span>
                @endif
            </a>
        @endforeach
    </nav>
</div>
