/**
 * Nested menu tree SortableJS controller (max 4 levels).
 * Bundled via Vite — do not load Sortable from CDN.
 * On drag end: serialize full tree + version token → POST reorder.
 */

import { register } from './registry.js';
import Sortable from 'sortablejs';

const MAX_DEPTH = 4;
const GROUP = 'menu-tree';
const ROOT_INSTANCES = new WeakMap();

/** Depth of an item dropped into `ul` (1 = root list). */
function getNestDepth(ul) {
    let depth = 1;
    let el = ul;
    while (el) {
        if (el.classList?.contains('menu-children')) depth++;
        if (el.id === 'menu-root') break;
        el = el.parentElement;
    }
    return depth;
}

/** Relative height of a dragged li subtree (1 = leaf). */
function subtreeHeight(li) {
    const childUl = li.querySelector(':scope > ul.menu-children');
    if (!childUl) return 1;
    const kids = [...childUl.querySelectorAll(':scope > li.menu-node')];
    if (kids.length === 0) return 1;
    return 1 + Math.max(...kids.map(subtreeHeight));
}

function serializeTree(ul) {
    const items = [];
    if (!ul) return items;
    for (const li of ul.querySelectorAll(':scope > li.menu-node')) {
        const id = parseInt(li.dataset.id, 10);
        const childUl = li.querySelector(':scope > ul.menu-children');
        const children = childUl ? serializeTree(childUl) : [];
        items.push({ id, children });
    }
    return items;
}

function countDepth(nodes, current) {
    if (!nodes || nodes.length === 0) return current;
    return Math.max(...nodes.map((n) => countDepth(n.children, current + 1)));
}

function showStatus(root, msg, type) {
    const statusEl = root.querySelector('[data-tree-status]');
    if (!statusEl) return;
    statusEl.textContent = msg;
    statusEl.className =
        'rounded px-4 py-2 text-sm ' +
        (type === 'error'
            ? 'bg-red-50 border border-red-200 text-red-700'
            : 'bg-green-50 border border-green-200 text-green-700');
    statusEl.classList.remove('hidden');
    setTimeout(() => statusEl.classList.add('hidden'), 5000);
}

async function sendReorder(root) {
    const rootUl = root.querySelector('#menu-root');
    const reorderUrl = root.dataset.reorderUrl;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const tree = serializeTree(rootUl);
    const maxD = countDepth(tree, 0);

    if (maxD > MAX_DEPTH) {
        showStatus(root, `Cây quá sâu (${maxD} cấp). Tối đa ${MAX_DEPTH} cấp.`, 'error');
        setTimeout(() => window.location.reload(), 1200);
        return;
    }

    try {
        const resp = await fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                tree,
                version: root.dataset.version || '',
            }),
        });

        if (resp.status === 409) {
            showStatus(root, 'Cây menu đã thay đổi bởi người khác. Tải lại trang để cập nhật.', 'error');
            return;
        }

        if (!resp.ok) {
            const data = await resp.json().catch(() => ({}));
            const msg = data?.message || data?.errors?.tree?.[0] || 'Sắp xếp thất bại.';
            showStatus(root, msg, 'error');
            setTimeout(() => window.location.reload(), 1200);
            return;
        }

        const data = await resp.json();
        if (data.version) {
            root.dataset.version = data.version;
        }
        if (data.reload) {
            showStatus(root, 'Đã lưu thứ tự menu.', 'success');
        }
    } catch {
        showStatus(root, 'Lỗi kết nối. Thử lại.', 'error');
    }
}

function attachSortable(ul, root, instances) {
    if (Sortable.get(ul)) return;

    const instance = Sortable.create(ul, {
        group: { name: GROUP, pull: true, put: true },
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'opacity-40',
        dragClass: 'shadow-lg',

        onMove(evt) {
            const targetDepth = getNestDepth(evt.to);
            const draggedHeight = subtreeHeight(evt.dragged);
            if (targetDepth + draggedHeight - 1 > MAX_DEPTH) return false;

            const dragged = evt.dragged;
            let container = evt.to;
            while (container) {
                if (container === dragged) return false;
                container = container.parentElement;
            }

            return true;
        },

        onEnd() {
            root.querySelectorAll('ul.menu-children').forEach((childUl) => {
                attachSortable(childUl, root, instances);
            });
            sendReorder(root);
        },
    });

    instances.push(instance);
}

register('[data-menu-tree]', {
    init(root) {
        const instances = [];
        const rootUl = root.querySelector('#menu-root');
        if (rootUl) attachSortable(rootUl, root, instances);
        root.querySelectorAll('ul.menu-children').forEach((ul) => attachSortable(ul, root, instances));
        ROOT_INSTANCES.set(root, instances);
    },
    destroy(root) {
        const instances = ROOT_INSTANCES.get(root) || [];
        instances.forEach((s) => s.destroy());
        ROOT_INSTANCES.delete(root);
    },
});
