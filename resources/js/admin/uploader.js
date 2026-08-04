/**
 * FilePond uploader: two-phase upload (stage → commit).
 * Dynamic import to keep initial bundle smaller.
 */

import { register } from './registry.js';

const POND_INSTANCES = new WeakMap();

register('[data-filepond]', {
    async init(el) {
        const [{ default: FilePond }, { default: FilePondPluginImagePreview }] =
            await Promise.all([
                import('filepond'),
                import('filepond-plugin-image-preview'),
            ]);

        FilePond.registerPlugin(FilePondPluginImagePreview);

        const name = el.dataset.name || 'file';
        const multiple = el.dataset.multiple === 'true';
        const processUrl = el.dataset.processUrl;
        const revertUrl = el.dataset.revertUrl;
        const existing = el.dataset.existing ? JSON.parse(el.dataset.existing) : [];

        // CSRF token from meta tag
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const pond = FilePond.create(el, {
            allowMultiple: multiple,
            name: multiple ? `${name}[]` : name,
            server: {
                process: {
                    url: processUrl,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    withCredentials: false,
                    onload: (response) => response,
                    onerror: (response) => response,
                },
                revert: {
                    url: revertUrl,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf },
                },
            },
            files: existing.map((src) => ({
                source: src,
                options: { type: 'local' },
            })),
            labelIdle: 'Kéo thả hoặc <span class="filepond--label-action">chọn file</span>',
            labelFileProcessing: 'Đang tải lên…',
            labelFileProcessingComplete: 'Tải lên xong',
            labelFileProcessingAborted: 'Đã hủy',
            labelFileProcessingError: 'Lỗi tải lên',
            labelFileRemoveError: 'Lỗi xóa file',
            labelTapToCancel: 'Nhấn để hủy',
            labelTapToRetry: 'Nhấn để thử lại',
            labelTapToUndo: 'Nhấn để hoàn tác',
            labelButtonRemoveItem: 'Xóa',
            labelButtonAbortItemLoad: 'Hủy',
            labelButtonRetryItemLoad: 'Thử lại',
            labelButtonAbortItemProcessing: 'Hủy',
            labelButtonUndoItemProcessing: 'Hoàn tác',
            labelButtonRetryItemProcessing: 'Thử lại',
            labelButtonProcessItem: 'Tải lên',
        });

        POND_INSTANCES.set(el, pond);
    },
    destroy(el) {
        POND_INSTANCES.get(el)?.destroy();
        POND_INSTANCES.delete(el);
    },
});
