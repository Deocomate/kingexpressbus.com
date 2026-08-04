{{-- Admin sidebar: collapsible, state persisted in localStorage via Alpine store --}}
<aside
    x-data
    :class="$store.sidebar.collapsed ? 'w-16' : 'w-64'"
    class="flex-shrink-0 flex flex-col bg-white border-r border-gray-200 transition-all duration-300 overflow-hidden"
    style="min-height: 100vh;"
>
    {{-- Logo / Brand --}}
    <div class="flex items-center h-16 px-4 border-b border-gray-200 flex-shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 min-w-0">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded bg-amber-500 text-white font-bold text-sm flex-shrink-0">K</span>
            <span
                x-show="!$store.sidebar.collapsed"
                x-transition:enter="transition-opacity duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="text-sm font-semibold text-gray-900 whitespace-nowrap"
            >KingExpressBus</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-2">
        @foreach(\App\Support\Admin\Navigation::items() as $item)
            <a
                href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-2 py-2 rounded text-sm font-medium transition-colors duration-150
                    {{ $item['active']
                        ? 'bg-amber-50 text-amber-700'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                :title="$store.sidebar.collapsed ? '{{ $item['label'] }}' : ''"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5 flex-shrink-0"
                    aria-hidden="true"
                >{!! $item['icon'] !!}</svg>
                <span
                    x-show="!$store.sidebar.collapsed"
                    x-transition:enter="transition-opacity duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="truncate"
                >{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Collapse toggle --}}
    <div class="flex-shrink-0 border-t border-gray-200 p-2">
        <button
            type="button"
            @click="$store.sidebar.toggle()"
            class="flex items-center justify-center w-full p-2 rounded text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
            :title="$store.sidebar.collapsed ? 'Mở rộng sidebar' : 'Thu gọn sidebar'"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                <path x-show="!$store.sidebar.collapsed" stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                <path x-show="$store.sidebar.collapsed" stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span
                x-show="!$store.sidebar.collapsed"
                class="ml-2 text-xs whitespace-nowrap"
            >Thu gọn</span>
        </button>
    </div>
</aside>
