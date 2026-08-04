@php
    $tabDefs = [
        'provinces'      => 'Tỉnh/Thành phố',
        'district-types' => 'Loại địa điểm',
        'districts'      => 'Địa điểm',
        'stops'          => 'Điểm dừng',
    ];
@endphp
<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex gap-1" aria-label="Sections">
        @foreach($tabDefs as $tabKey => $tabLabel)
            <a
                href="{{ route('admin.locations.index', ['section' => $tabKey]) }}"
                class="inline-flex items-center px-4 py-2.5 text-sm font-medium border-b-2 whitespace-nowrap transition-colors
                    {{ $activeSection === $tabKey
                        ? 'border-brand-500 text-brand-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                aria-current="{{ $activeSection === $tabKey ? 'page' : 'false' }}"
            >
                {{ $tabLabel }}
            </a>
        @endforeach
    </nav>
</div>
