@php
use App\Models\Menu;
use App\Models\Route as RouteModel;
use App\Support\Admin\MenuTreeBuilder;

$flatRecords  = Menu::treeRecords();
$tree         = MenuTreeBuilder::build($flatRecords);
$versionToken = MenuTreeBuilder::versionToken();
$reorderUrl   = route('admin.website.menus.reorder');

// Parent options for the create/edit form: show all menus except deep ones
$parentOptions = $flatRecords->mapWithKeys(fn ($m) => [$m->id => str_repeat('— ', $m->parent_id === Menu::ROOT_PARENT_ID ? 0 : 1) . $m->name]);

// Route options for type=route
$routeOptions = RouteModel::orderBy('name')->pluck('name', 'id')->toArray();
@endphp

<div class="space-y-4">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Kéo-thả để sắp xếp. Tối đa 4 cấp.
            <strong>{{ $flatRecords->count() }}</strong> menu.
        </p>
        <button
            type="button"
            @click="$dispatch('open-slide-over', { id: 'menu-form-slide-over' })"
            class="inline-flex items-center gap-1.5 rounded bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm menu
        </button>
    </div>

    {{-- Status messages --}}
    <div id="tree-status" class="hidden rounded px-4 py-2 text-sm" role="alert"></div>

    {{-- Tree --}}
    <x-admin::form.section>
        @if($flatRecords->isEmpty())
        <p class="text-sm text-gray-500 text-center py-6">Chưa có menu nào.</p>
        @else
        <ul id="menu-root" class="space-y-1">
            @include('admin.website._menu-node', ['nodes' => $tree])
        </ul>
        @endif
    </x-admin::form.section>
</div>

{{-- Menu create/edit slide-over --}}
<x-admin::display.slide-over id="menu-form-slide-over" title="Menu" width="max-w-lg">
    @include('admin.website._menu-form', [
        'parentOptions' => $parentOptions,
        'routeOptions'  => $routeOptions,
    ])
</x-admin::display.slide-over>

{{-- Sortable tree JS --}}
<script>
(function() {
    'use strict';

    const MAX_DEPTH     = 4;
    const ROOT_PARENT   = {{ \App\Models\Menu::ROOT_PARENT_ID }};
    const REORDER_URL   = @json($reorderUrl);
    const VERSION_TOKEN = @json($versionToken);
    const CSRF          = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const statusEl = document.getElementById('tree-status');

    function showStatus(msg, type) {
        statusEl.textContent = msg;
        statusEl.className   = 'rounded px-4 py-2 text-sm ' + (type === 'error'
            ? 'bg-red-50 border border-red-200 text-red-700'
            : 'bg-green-50 border border-green-200 text-green-700');
        statusEl.classList.remove('hidden');
        setTimeout(() => statusEl.classList.add('hidden'), 5000);
    }

    function getDepth(el) {
        let depth = 0;
        let cur   = el.parentElement;
        while (cur) {
            if (cur.classList.contains('menu-children')) depth++;
            cur = cur.parentElement;
        }
        return depth;
    }

    function serializeTree(ul) {
        const items = [];
        for (const li of ul.querySelectorAll(':scope > li.menu-node')) {
            const id       = parseInt(li.dataset.id, 10);
            const childUl  = li.querySelector(':scope > ul.menu-children');
            const children = childUl ? serializeTree(childUl) : [];
            items.push({ id, children });
        }
        return items;
    }

    function countDepth(nodes, current) {
        if (!nodes || nodes.length === 0) return current;
        return Math.max(...nodes.map(n => countDepth(n.children, current + 1)));
    }

    async function sendReorder() {
        const tree = serializeTree(document.getElementById('menu-root'));
        const maxD = countDepth(tree, 0);
        if (maxD > MAX_DEPTH) {
            showStatus(`Cây quá sâu (${maxD} cấp). Tối đa ${MAX_DEPTH} cấp.`, 'error');
            return;
        }

        try {
            const resp = await fetch(REORDER_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ tree, version: VERSION_TOKEN }),
            });

            if (resp.status === 409) {
                showStatus('Cây menu đã thay đổi bởi người khác. Tải lại trang để cập nhật.', 'error');
                return;
            }

            if (!resp.ok) {
                const data = await resp.json().catch(() => ({}));
                const msg  = data?.message || data?.errors?.tree?.[0] || 'Sắp xếp thất bại.';
                showStatus(msg, 'error');
                return;
            }

            const data = await resp.json();
            if (data.reload) {
                showStatus('Đã lưu thứ tự menu.', 'success');
            }
        } catch (err) {
            showStatus('Lỗi kết nối. Thử lại.', 'error');
        }
    }

    // Dynamically load SortableJS from CDN then initialise all ul.menu-children
    async function initSortable() {
        const { default: Sortable } = await import(
            'https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/+esm'
        );

        const GROUP = 'menu-tree';

        function attachSortable(ul) {
            Sortable.create(ul, {
                group:      { name: GROUP, pull: true, put: true },
                animation:  150,
                handle:     '.drag-handle',
                ghostClass: 'opacity-40',
                dragClass:  'shadow-lg',

                onMove(evt) {
                    // Block drag if target depth would exceed MAX_DEPTH
                    const targetDepth = getDepth(evt.to) + 1;
                    if (targetDepth > MAX_DEPTH) return false;

                    // Block dragging a node into its own descendant
                    const dragged = evt.dragged;
                    let container = evt.to;
                    while (container) {
                        if (container === dragged) return false;
                        container = container.parentElement;
                    }

                    return true;
                },

                onEnd() {
                    // Re-attach sortable to any new child ul that was empty before
                    document.querySelectorAll('ul.menu-children').forEach(childUl => {
                        if (!Sortable.get(childUl)) attachSortable(childUl);
                    });
                    sendReorder();
                },
            });
        }

        // Attach to root and all child lists
        const rootUl = document.getElementById('menu-root');
        if (rootUl) attachSortable(rootUl);
        document.querySelectorAll('ul.menu-children').forEach(ul => attachSortable(ul));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSortable);
    } else {
        initSortable();
    }
})();
</script>
