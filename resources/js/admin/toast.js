/**
 * Toast notifications via Alpine store.
 * Already defined in admin.js; this module re-exports the Alpine store API
 * for convenience and wires flash session messages loaded from DOM.
 */

export function initFlashMessages() {
    const flashEl = document.getElementById('admin-flash-messages');
    if (!flashEl || !window.Alpine) return;

    flashEl.querySelectorAll('[data-flash]').forEach((el) => {
        const text = el.dataset.flashText;
        const type = el.dataset.flashType || 'info';
        if (text) {
            window.Alpine.store('toast')?.show(text, type);
        }
    });
}
