@props([
    'tabs'          => [],   // ['key' => 'label'] or [['key'=>..., 'label'=>...]]
    'activeSection' => '',
])
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex gap-1" aria-label="Sections">
        @foreach($tabs as $key => $label)
            @php
                if (is_array($label)) {
                    $sKey   = $label['key'];
                    $sLabel = $label['label'];
                } else {
                    $sKey   = $key;
                    $sLabel = $label;
                }
                $isActive = $activeSection === $sKey;
            @endphp
            <a
                href="{{ request()->fullUrlWithQuery(['section' => $sKey]) }}"
                class="inline-flex items-center px-4 py-2.5 text-sm font-medium border-b-2 whitespace-nowrap transition-colors
                    {{ $isActive
                        ? 'border-brand-500 text-brand-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                aria-current="{{ $isActive ? 'page' : 'false' }}"
            >
                {{ $sLabel }}
            </a>
        @endforeach
    </nav>
</div>
