/**
 * SweetAlert2 confirmation dialogs (Vietnamese).
 * Wires [data-confirm] attributes to show a dialog before form submit or link navigation.
 */

import { register } from './registry.js';

register('[data-confirm]', {
    init(el) {
        el._confirmHandler = async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const { default: Swal } = await import('sweetalert2');

            const title = el.dataset.confirmTitle || 'Bạn có chắc không?';
            const text = el.dataset.confirmText || 'Hành động này không thể hoàn tác.';
            const confirmText = el.dataset.confirmYes || 'Xác nhận';
            const cancelText = el.dataset.confirmNo || 'Hủy';
            const icon = el.dataset.confirmIcon || 'warning';

            const result = await Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                confirmButtonColor: '#ef4444',
                reverseButtons: true,
            });

            if (!result.isConfirmed) return;

            // Submit form or follow link
            if (el.tagName === 'FORM') {
                el._confirming = true;
                el.submit();
            } else if (el.tagName === 'A') {
                window.location.href = el.href;
            } else {
                const form = el.closest('form');
                if (form) {
                    form.submit();
                }
            }
        };

        el.addEventListener(el.tagName === 'FORM' ? 'submit' : 'click', el._confirmHandler);
    },
    destroy(el) {
        if (el._confirmHandler) {
            el.removeEventListener(el.tagName === 'FORM' ? 'submit' : 'click', el._confirmHandler);
            delete el._confirmHandler;
        }
    },
});
