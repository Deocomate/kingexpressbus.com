/**
 * Shopify Draggable (Sortable module) controller for row reordering.
 * Flat, single-container lists only — see menu-tree.js for nested tree drag&drop.
 * On drag end, POSTs { ids: [...] } — matches every *Controller::reorder() (Bus,
 * Route, District, Province, DistrictType, Stop), unchanged.
 *
 * Drag MUST use [data-drag-handle]. Whole-row dragging intercepts clicks on
 * action links (Sửa/Xóa) inside the row — never fall back to row-as-handle.
 */

import { register } from './registry.js';
import { Sortable } from '@shopify/draggable';

// Expose for the standalone Alpine component in routes/_stops-block.blade.php,
// which manages its own Sortable instance outside the [data-sortable] registry
// (its rows are Alpine x-for templated, not server-rendered on load).
window.ShopifyDraggable = { Sortable };

const SORTABLE_INSTANCES = new WeakMap();

function toastError(message) {
    window.Alpine?.store('toast')?.error(message);
}

function syncPriorityCells(el, ids) {
    const total = ids.length;
    ids.forEach((id, index) => {
        const row = el.querySelector(`[data-sortable-id="${id}"]`);
        const cell = row?.querySelector('[data-priority-value]');
        if (cell) {
            cell.textContent = String(total - index);
        }
    });
}

register('[data-sortable]', {
    init(el) {
        const reorderUrl = el.dataset.reorderUrl;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        if (!el.querySelector('[data-drag-handle]')) {
            console.warn('[sortable] missing [data-drag-handle] — drag disabled to protect row actions', el);
            return;
        }

        const instance = new Sortable([el], {
            draggable: '[data-sortable-id]',
            handle: '[data-drag-handle]',
            mirror: { appendTo: 'body', constrainDimensions: true },
            classes: {
                mirror: 'shadow-lg',
                'source:dragging': 'bg-brand-50',
            },
        });

        // The mirror is a raw clone appended to <body>, outside the original
        // x-data scope. Alpine's mutation observer would otherwise try to init
        // any x-model/x-on directives copied along with the row (e.g. the bulk
        // "selected" checkbox) and throw ReferenceErrors resolving against a
        // scope that doesn't exist there.
        instance.on('mirror:created', ({ mirror }) => mirror.setAttribute('x-ignore', ''));

        instance.on('sortable:stop', () => {
            if (!reorderUrl || reorderUrl === '#') return;

            const items = [...el.querySelectorAll('[data-sortable-id]')];
            const ids = items.map((item) => item.dataset.sortableId).filter(Boolean);

            if (ids.length === 0) return;

            fetch(reorderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ ids }),
            })
                .then(async (response) => {
                    if (!response.ok) {
                        const payload = await response.json().catch(() => ({}));
                        throw new Error(payload.message || `Reorder failed (${response.status})`);
                    }
                    syncPriorityCells(el, ids);
                })
                .catch((err) => {
                    console.error('[sortable] reorder error', err);
                    toastError('Không lưu được thứ tự. Tải lại trang và thử lại.');
                });
        });

        SORTABLE_INSTANCES.set(el, instance);
    },
    destroy(el) {
        SORTABLE_INSTANCES.get(el)?.destroy();
        SORTABLE_INSTANCES.delete(el);
    },
});
