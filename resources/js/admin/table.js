/**
 * Table engine: intercepts filter form submits, pagination clicks, and tab
 * clicks; fetches an HTML partial (X-Partial: table) and swaps it into the
 * `[data-table]` container's innerHTML — the container itself is never
 * destroyed, so its own listeners never need to be re-attached.
 * Also drives the generic toggle-cell switch and bulk-selection bar via
 * event delegation, since neither holds per-element state that would
 * require an init/destroy lifecycle.
 */

import { register, initTree, destroyTree } from './registry.js';

/** Active AbortController per table container. */
const abortControllers = new WeakMap();

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

register('[data-table]', {
    init(tableContainer) {
        // Attach submit listener on filter form (lives outside the container)
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

        // Pagination and tab clicks (delegated, survives content swaps)
        tableContainer._tableClickHandler = (e) => {
            const link = e.target.closest('[data-table-page], [data-tab-key]');
            if (!link || link.tagName !== 'A') return;
            e.preventDefault();
            fetchTable(link.href, tableContainer);
        };
        tableContainer.addEventListener('click', tableContainer._tableClickHandler);
    },
    destroy(tableContainer) {
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
 * Fetch a table partial and swap it into the container's innerHTML.
 * The container element itself is kept in the DOM (never replaced), so
 * listeners bound to it in init() above stay valid across every swap.
 * @param {string} url
 * @param {Element} tableContainer
 */
async function fetchTable(url, tableContainer) {
    // Cancel previous in-flight request for this container
    abortControllers.get(tableContainer)?.abort();
    const ctrl = new AbortController();
    abortControllers.set(tableContainer, ctrl);

    tableContainer.classList.add('opacity-50', 'pointer-events-none');

    try {
        const resp = await fetch(url, {
            headers: { 'X-Partial': 'table', 'X-Requested-With': 'XMLHttpRequest' },
            signal: ctrl.signal,
        });

        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const html = await resp.text();

        // Destroy controllers bound to the old content (datepickers, sortable, ...)
        destroyTree(tableContainer);

        tableContainer.innerHTML = html;

        // Re-init controllers for the freshly inserted content
        initTree(tableContainer);
        refreshAllBulkBars();

        window.history.pushState({}, '', url);
    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error('[table] fetch error', err);
        }
    } finally {
        tableContainer.classList.remove('opacity-50', 'pointer-events-none');
    }
}

// Handle browser back/forward
window.addEventListener('popstate', () => {
    const tableContainer = document.querySelector('[data-table]');
    if (tableContainer) {
        fetchTable(window.location.href, tableContainer);
    }
});

// -----------------------------------------------------------------------
// Toggle-cell: AJAX on/off switch, no full reload.
// Delegated at document level so it survives any [data-table] content swap.
// -----------------------------------------------------------------------

document.addEventListener('click', async (e) => {
    const cell = e.target.closest('[data-toggle-cell]');
    if (!cell) return;
    const btn = e.target.closest('button');
    if (!btn) return;

    const url = cell.dataset.url;
    if (!url || btn.disabled) return;

    const method = cell.dataset.method || 'PATCH';
    const wasOn = btn.getAttribute('aria-pressed') === 'true';

    btn.disabled = true;
    try {
        const resp = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
        });
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const data = await resp.json().catch(() => ({}));
        const isOn = typeof data.is_active === 'boolean' ? data.is_active : !wasOn;
        applyToggleState(btn, isOn);
        window.Alpine?.store('toast')?.success('Đã cập nhật.');
    } catch (err) {
        console.error('[toggle-cell] error', err);
        window.Alpine?.store('toast')?.error('Không thể cập nhật, vui lòng thử lại.');
    } finally {
        btn.disabled = false;
    }
});

function applyToggleState(btn, isOn) {
    btn.setAttribute('aria-pressed', isOn ? 'true' : 'false');
    btn.classList.toggle('bg-brand-500', isOn);
    btn.classList.toggle('bg-gray-200', !isOn);
    const knob = btn.querySelector('span[aria-hidden]');
    knob?.classList.toggle('translate-x-4', isOn);
    knob?.classList.toggle('translate-x-0', !isOn);
}

// -----------------------------------------------------------------------
// Bulk selection: select-all / row checkboxes / bulk action bar.
// Pure event delegation — no per-element init/destroy needed, so it keeps
// working no matter how the surrounding table markup gets swapped.
// -----------------------------------------------------------------------

const ROW_CHECK_SELECTOR = '[data-row-check], [data-row-checkbox]';

/**
 * Nearest ancestor (starting at el) that contains both a <table> and a
 * [data-bulk-bar] among its descendants. Handles the two layouts used
 * across admin tables: bulk-bar nested inside [data-table], or bulk-bar
 * and [data-table] as siblings inside a shared wrapping <form>.
 */
function findTableScope(el) {
    let node = el;
    while (node && node !== document) {
        if (node.querySelector?.('table') && node.querySelector('[data-bulk-bar]')) {
            return node;
        }
        node = node.parentElement;
    }
    return document;
}

function recomputeBulkBar(scope) {
    const bar = scope.querySelector('[data-bulk-bar]');
    if (!bar) return;

    const boxes = scope.querySelectorAll(ROW_CHECK_SELECTOR);
    const checked = scope.querySelectorAll(`${ROW_CHECK_SELECTOR}:checked`);

    bar.classList.toggle('hidden', checked.length === 0);
    bar.classList.toggle('flex', checked.length > 0);

    const countEl = bar.querySelector('[data-bulk-count]');
    if (countEl) countEl.textContent = `Đã chọn ${checked.length}`;

    const selectAll = scope.querySelector('[data-select-all]');
    if (selectAll) {
        selectAll.checked = boxes.length > 0 && checked.length === boxes.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < boxes.length;
    }
}

/** Reset every bulk bar on the page (called after a table refresh). */
function refreshAllBulkBars() {
    document.querySelectorAll('[data-bulk-bar]').forEach((bar) => {
        recomputeBulkBar(findTableScope(bar));
    });
}

document.addEventListener('change', (e) => {
    if (e.target.matches('[data-select-all]')) {
        const scope = findTableScope(e.target);
        scope.querySelectorAll(ROW_CHECK_SELECTOR).forEach((cb) => {
            cb.checked = e.target.checked;
        });
        recomputeBulkBar(scope);
        return;
    }

    if (e.target.matches(ROW_CHECK_SELECTOR)) {
        recomputeBulkBar(findTableScope(e.target));
    }
});

document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-bulk-action]');
    if (!btn || btn.disabled) return;

    const form = btn.closest('form');
    if (!form) return;

    const checked = form.querySelectorAll(`${ROW_CHECK_SELECTOR}:checked`);
    if (checked.length === 0) return;

    const { default: Swal } = await import('sweetalert2');
    const result = await Swal.fire({
        title: 'Bạn có chắc không?',
        text: `Xóa ${checked.length} mục đã chọn? Hành động này không thể hoàn tác.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#ef4444',
        reverseButtons: true,
    });
    if (!result.isConfirmed) return;

    btn.disabled = true;
    try {
        const resp = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            body: new FormData(form),
        });

        // Bulk endpoints respond with JSON for AJAX requests: {success, message}.
        // A 422 means the action was blocked (e.g. by DeleteGuard) — that is
        // still a "successful" HTTP exchange, so don't throw on it.
        const data = await resp.json().catch(() => null);
        if (!resp.ok && resp.status !== 422) throw new Error(`HTTP ${resp.status}`);

        const success = data?.success ?? resp.ok;
        const message = data?.message || (success ? `Đã xóa ${checked.length} mục.` : 'Không thể xóa, vui lòng thử lại.');
        window.Alpine?.store('toast')?.[success ? 'success' : 'error'](message);

        const tableContainer = form.querySelector('[data-table]') || document.querySelector('[data-table]');
        if (tableContainer) {
            fetchTable(window.location.href, tableContainer);
        } else {
            window.location.reload();
        }
    } catch (err) {
        console.error('[bulk-action] error', err);
        window.Alpine?.store('toast')?.error('Không thể xóa, vui lòng thử lại.');
    } finally {
        btn.disabled = false;
    }
});

export { fetchTable };
