import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);

// -----------------------------------------------------------------------
// Alpine stores
// -----------------------------------------------------------------------

// Sidebar store: persist collapsed state in localStorage
Alpine.store('sidebar', {
    collapsed: localStorage.getItem('admin_sidebar_collapsed') === 'true',

    toggle() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('admin_sidebar_collapsed', this.collapsed);
    },

    collapse() {
        this.collapsed = true;
        localStorage.setItem('admin_sidebar_collapsed', 'true');
    },

    expand() {
        this.collapsed = false;
        localStorage.setItem('admin_sidebar_collapsed', 'false');
    },
});

// Toast store: Alpine-only, no jQuery dependency
Alpine.store('toast', {
    messages: [],
    _counter: 0,

    init() {},

    show(text, type = 'info', duration = 4000) {
        const id = ++this._counter;
        this.messages.push({ id, text, type, visible: true });
        setTimeout(() => this.dismiss(id), duration);
    },

    dismiss(id) {
        const msg = this.messages.find(m => m.id === id);
        if (msg) {
            msg.visible = false;
            setTimeout(() => {
                this.messages = this.messages.filter(m => m.id !== id);
            }, 300);
        }
    },

    success(text) { this.show(text, 'success'); },
    error(text) { this.show(text, 'error'); },
    warning(text) { this.show(text, 'warning'); },
    info(text) { this.show(text, 'info'); },
});

window.Alpine = Alpine;
Alpine.start();

// -----------------------------------------------------------------------
// Import lightweight modules synchronously (small, always needed)
// -----------------------------------------------------------------------

import { initTree, destroyTree } from './admin/registry.js';

import './admin/form-controls.js';
import './admin/table.js';
import './admin/sortable.js';
import './admin/menu-tree.js';
import './admin/confirm.js';

// Heavy modules loaded lazily
import './admin/uploader.js';
import './admin/chart.js';
import './admin/rich-text.js';

// Expose for inline Alpine pollers / partial swaps outside [data-table]
window.AdminRegistry = { initTree, destroyTree };

// -----------------------------------------------------------------------
// Initialize on DOM ready
// -----------------------------------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    initTree(document.body);
});
