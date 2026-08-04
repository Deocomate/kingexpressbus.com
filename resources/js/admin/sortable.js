/**
 * SortableJS controller for row reordering.
 * Supports flat table reorder and nested tree (x-group + maxDepth).
 * On drag end, POSTs one request with serialized order array.
 */

import { register } from './registry.js';
import Sortable from 'sortablejs';

const SORTABLE_INSTANCES = new WeakMap();

register('[data-sortable]', {
    init(el) {
        const reorderUrl = el.dataset.reorderUrl;
        const group = el.dataset.sortableGroup || undefined;
        const maxDepth = el.dataset.maxDepth ? Number(el.dataset.maxDepth) : undefined;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const instance = Sortable.create(el, {
            group: group ? { name: group, pull: true, put: true } : undefined,
            animation: 150,
            handle: '[data-drag-handle]',
            ghostClass: 'bg-brand-50',
            dragClass: 'shadow-lg',
            onEnd() {
                if (!reorderUrl) return;

                // Collect all sortable item IDs in current order
                const items = [...el.querySelectorAll('[data-sortable-id]')];
                const order = items.map((item) => item.dataset.sortableId);

                fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ order }),
                }).catch((err) => console.error('[sortable] reorder error', err));
            },
        });

        SORTABLE_INSTANCES.set(el, instance);
    },
    destroy(el) {
        SORTABLE_INSTANCES.get(el)?.destroy();
        SORTABLE_INSTANCES.delete(el);
    },
});
