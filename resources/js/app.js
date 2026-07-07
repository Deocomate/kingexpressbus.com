import './modules/jquery-bootstrap';
import toastr from 'toastr';
import flatpickr from 'flatpickr';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import { kingSearchBar } from './modules/search-bar';
import { initReveals } from './modules/reveal';
import { configureToastr } from './modules/toastr-setup';

// Runtime globals relied on by existing Blade views must exist before
// Alpine.start() runs, otherwise x-data="kingSearchBar(...)" throws and the
// search CTA dies silently.
window.flatpickr = flatpickr;
window.kingSearchBar = kingSearchBar;
window.Swal = Swal;
window.toastr = toastr;

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    configureToastr(toastr);
    initReveals();

    const flash = window.__kingFlashMessages || {};
    ['success', 'error', 'warning', 'info'].forEach((level) => {
        (flash[level] || []).forEach((message) => toastr[level](message));
    });
});
