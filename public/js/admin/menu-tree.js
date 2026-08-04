/**
 * Nested menu tree drag&drop controller (max 4 levels) via Shopify Draggable.
 * Every menu node always renders a `ul.menu-children` drop target (populated or
 * empty — see _menu-node.blade.php), so all containers exist upfront; no need to
 * track newly created containers after a drop.
 * On drag end: serialize full tree + version token → POST reorder (contract unchanged).
 */

import { register } from './registry.js';
import { Sortable } from '@shopify/draggable';

const MAX_DEPTH = 4;
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

/** True if `container` is `dragged` itself or nested inside it (would create a cycle). */
function isDescendantOrSelf(container, dragged) {
    let el = container;
    while (el) {
        if (el === dragged) return true;
        el = el.parentElement;
    }
    return false;
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

register('[data-menu-tree]', {
    init(root) {
        const rootUl = root.querySelector('#menu-root');
        if (!rootUl) return;

        const containers = [rootUl, ...root.querySelectorAll('ul.menu-children')];

        const instance = new Sortable(containers, {
            draggable: 'li.menu-node',
            handle: '.drag-handle',
            mirror: { appendTo: 'body', constrainDimensions: true },
            classes: {
                mirror: 'shadow-lg',
                'source:dragging': 'opacity-40',
            },
        });

        // See sortable.js: the mirror clone (appended to <body>) escapes the
        // original Alpine scope, so ignore it to avoid ReferenceErrors from any
        // x-on/x-model directives copied along with the dragged node.
        instance.on('mirror:created', ({ mirror }) => mirror.setAttribute('x-ignore', ''));

        // Preventive block: cancel the sort in-flight if it would exceed MAX_DEPTH
        // or drop a node into its own subtree — mirrors the old SortableJS onMove guard.
        instance.on('sortable:sort', (evt) => {
            const overContainer = evt.overContainer;
            const dragged = evt.dragEvent?.source;
            if (!overContainer || !dragged) return;

            if (isDescendantOrSelf(overContainer, dragged)) {
                evt.cancel();
                return;
            }

            const targetDepth = getNestDepth(overContainer);
            const draggedHeight = subtreeHeight(dragged);
            if (targetDepth + draggedHeight - 1 > MAX_DEPTH) {
                evt.cancel();
            }
        });

        instance.on('sortable:stop', () => {
            sendReorder(root);
        });

        ROOT_INSTANCES.set(root, instance);
    },
    destroy(root) {
        ROOT_INSTANCES.get(root)?.destroy();
        ROOT_INSTANCES.delete(root);
    },
});
