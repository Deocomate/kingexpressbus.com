@foreach($nodes as $entry)
@php
    /** @var \App\Models\Menu $menu */
    $menu     = $entry['node'];
    $children = $entry['children'];

    $typeBadge = match ($menu->type) {
        'route'       => '🚌 Tuyến đường',
        'page'        => '📄 Trang tĩnh',
        'system_page' => '⚙️ Trang hệ thống',
        default       => '🔗 Liên kết',   // custom_link
    };
    $target = $menu->type === 'route' ? "ID: {$menu->related_id}" : ($menu->url ?: '—');
@endphp
<li
    class="menu-node"
    data-id="{{ $menu->id }}"
>
    <div class="flex items-center gap-2 rounded bg-white border border-gray-200 px-3 py-2 hover:border-gray-300 transition-colors group">
        {{-- Drag handle --}}
        <span
            class="drag-handle cursor-grab text-gray-300 hover:text-gray-500 select-none flex-none"
            title="Kéo để sắp xếp"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 2a1 1 0 011 1v1h4V3a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm0 6a1 1 0 000 2h6a1 1 0 000-2H7z"/>
            </svg>
        </span>

        {{-- Label --}}
        <div class="flex-1 min-w-0">
            <span class="font-medium text-gray-800 text-sm">{{ $menu->name }}</span>
            <span class="text-gray-400 mx-1 text-xs">—</span>
            <span class="text-xs text-gray-500">[{{ $typeBadge }} | {{ $target }}]</span>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-none">
            <button
                type="button"
                class="rounded px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                @click="$dispatch('open-slide-over', { id: 'menu-form-slide-over' }); setEditMenu({{ json_encode(['id' => $menu->id, 'name' => $menu->name, 'type' => $menu->type, 'url' => $menu->url, 'related_id' => $menu->related_id, 'parent_id' => $menu->parent_id, 'priority' => $menu->priority]) }})"
            >
                Sửa
            </button>
            <form method="POST" action="{{ route('admin.website.menus.destroy', $menu) }}"
                  @submit.prevent="if(confirm('Xóa menu này và toàn bộ menu con?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded px-2 py-1 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                    Xóa
                </button>
            </form>
        </div>
    </div>

    @if(count($children) > 0)
    <ul class="menu-children ml-6 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
        @include('admin.website._menu-node', ['nodes' => $children])
    </ul>
    @else
    {{-- Empty drop target for nesting --}}
    <ul class="menu-children ml-6 border-l-2 border-dashed border-gray-100 pl-3 min-h-[8px]"></ul>
    @endif
</li>
@endforeach
