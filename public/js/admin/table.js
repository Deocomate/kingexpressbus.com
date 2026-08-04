/**
 * Table engine: intercepts filter form submits and pagination clicks,
 * fetches HTML partial (X-Partial: table), swaps DOM, manages history.
 * Implements init(root)/destroy(root) for registry.
 */

import { register, initTree, destroyTree } from './registry.js';

/** Active AbortController per table container. */
const abortControllers = new WeakMap();

register('[data-table]', {
    init(tableContainer) {
        // Attach submit listener on filter form
        const filterForm = document.querySelector('[data-table-filter]');
        if (filterForm) {
            filterForm._tableSubmitHandler = (e) => {
                e.preventDefault();
                const url = new URL(filterForm.action || window.location.href);
                const data = new FormData(filterForm);
                data.forEach((v, k) => {
                    if (v !== '') url.searchParams.set(k, v);
                    else url.searchParams.delete(k);
                });
                url.searchParams.delete('page');
                fetchTable(url.toString(), tableContainer);
            };
            filterForm.addEventListener('submit', filterForm._tableSubmitHandler);
        }

        // Pagination and tab clicks
        tableContainer._tableClickHandler = (e) => {
            const link = e.target.closest('[data-table-page], [data-tab-key]');
            if (!link || link.tagName !== 'A') return;
            e.preventDefault();
            fetchTable(link.href, tableContainer);
        };
        tableContainer.addEventListener('click', tableContainer._tableClickHandler);
    },
    destroy(tableContainer) {
        // Abort any in-flight request
        abortControllers.get(tableContainer)?.abort();
        abortControllers.delete(tableContainer);

        tableContainer.removeEventListener('click', tableContainer._tableClickHandler);

        const filterForm = document.querySelector('[data-table-filter]');
        if (filterForm?._tableSubmitHandler) {
            filterForm.removeEventListener('submit', filterForm._tableSubmitHandler);
            delete filterForm._tableSubmitHandler;
        }
    },
});

/**
 * Fetch table partial and swap DOM.
 * @param {string} url
 * @param {Element} tableContainer
 */
async function fetchTable(url, tableContainer) {
    // Cancel previous request
    abortControllers.get(tableContainer)?.abort();
    const ctrl = new AbortController();
    abortControllers.set(tableContainer, ctrl);

    // Show loading state
    tableContainer.classList.add('opacity-50', 'pointer-events-none');

    try {
        const resp = await fetch(url, {
            headers: { 'X-Partial': 'table', 'X-Requested-With': 'XMLHttpRequest' },
            signal: ctrl.signal,
        });

        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const html = await resp.text();

        // Destroy old instances within container
        destroyTree(tableContainer);

        // Swap HTML
        tableContainer.outerHTML = html;

        // The new container is in the DOM — query it again
        const newContainer = document.querySelector('[data-table]');

        if (newContainer) {
            // Init new instances
            initTree(newContainer);
        }

        // Push URL without reloading
        window.history.pushState({}, '', url);

    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error('[table] fetch error', err);
            tableContainer.classList.remove('opacity-50', 'pointer-events-none');
        }
    }
}

// Handle browser back/forward
window.addEventListener('popstate', () => {
    const tableContainer = document.querySelector('[data-table]');
    if (tableContainer) {
        fetchTable(window.location.href, tableContainer);
    }
});
