/**
 * Dropzone 5.x uploader: two-phase upload (stage → commit), same contract as
 * before (UploadController::process/revert, UploadStager, unchanged backend).
 * Dropzone 5.x is UMD/classic-script only — loaded via <script src> in the admin
 * layout before this module runs, exposed as window.Dropzone (see _cdn-assets.blade.php).
 *
 * Hidden input contract (verified against BusController/RouteController/etc.
 * prepareData()): a submitted value containing '~' is a fresh upload token to
 * commit; a value without '~' is stored AS-IS, so an unchanged existing file
 * must resubmit its exact raw DB path (not the resolved display URL) or the
 * backend treats it as a literal new path — and, for the array (multiple) case,
 * an existing path missing from the submission gets deleted from storage.
 * Each hidden input lives inside its file's own Dropzone preview element so
 * drag-reordering the previews (multiple mode) reorders the submitted values
 * for free, via normal DOM order — no separate resync step needed.
 */

import { register } from './registry.js';
import { Sortable } from '@shopify/draggable';

const DROPZONE_INSTANCES = new WeakMap();

// Disable before any DOMContentLoaded autoDiscover can run. The classic-script
// tag in _cdn-assets also sets this; keep both so module-only loads stay safe.
if (window.Dropzone) {
    window.Dropzone.autoDiscover = false;
}

/**
 * Dropzone's constructor sets element.dropzone BEFORE validating options.url.
 * A failed autoDiscover therefore leaves a dead attachment that blocks our init.
 */
function detachStaleDropzone(el, Dropzone) {
    if (!el.dropzone) return;
    try {
        el.dropzone.destroy();
    } catch (err) {
        console.warn('[uploader] destroy stale Dropzone failed', err);
        delete el.dropzone;
        if (Array.isArray(Dropzone.instances)) {
            Dropzone.instances = Dropzone.instances.filter((instance) => instance.element !== el);
        }
    }
}

register('[data-dropzone]', {
    init(el) {
        const Dropzone = window.Dropzone;
        if (!Dropzone) {
            console.error('[uploader] window.Dropzone not loaded — check CDN <script> tag order');
            return;
        }
        Dropzone.autoDiscover = false;
        detachStaleDropzone(el, Dropzone);

        // CDN stylesheet scopes almost everything under `.dropzone`. Dropzone
        // does NOT add this class itself — it only injects dz-message when the
        // class is already present. Enforce it here so a missing Blade class
        // cannot silently break the UI again.
        el.classList.add('dropzone');

        const rawName = el.dataset.name || 'file';
        const multiple = el.dataset.multiple === 'true';
        const baseName = rawName.endsWith('[]') ? rawName.slice(0, -2) : rawName;
        const inputName = multiple ? `${baseName}[]` : baseName;
        const processUrl = el.dataset.processUrl;
        const revertUrl = el.dataset.revertUrl;
        const existingUrls = el.dataset.existing ? JSON.parse(el.dataset.existing) : [];
        const existingPaths = el.dataset.existingPaths ? JSON.parse(el.dataset.existingPaths) : [];
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const maxSizeBytes = el.dataset.maxSize ? Number(el.dataset.maxSize) : undefined;

        if (!processUrl) {
            console.error('[uploader] missing data-process-url on', el);
            return;
        }

        // Single mode only ever has 0 or 1 hidden input. When there is no file at
        // all, an explicit empty-value placeholder must still be submitted so the
        // backend sees "removed", not "field absent, keep existing" — tracked as a
        // single reference so attaching a real file-scoped input always replaces it.
        let emptyPlaceholder = null;
        function ensureEmptyPlaceholder() {
            if (multiple || emptyPlaceholder) return;
            emptyPlaceholder = document.createElement('input');
            emptyPlaceholder.type = 'hidden';
            emptyPlaceholder.name = inputName;
            emptyPlaceholder.value = '';
            el.appendChild(emptyPlaceholder);
        }
        function removeEmptyPlaceholder() {
            emptyPlaceholder?.remove();
            emptyPlaceholder = null;
        }

        function attachHiddenInput(file, value) {
            if (!multiple) removeEmptyPlaceholder();
            // Replacing a file reuses the preview — drop any prior hidden input.
            file.hiddenInput?.remove();
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName;
            input.value = typeof value === 'string' ? value : String(value ?? '');
            (file.previewElement || el).appendChild(input);
            file.hiddenInput = input;
            return input;
        }

        const dz = new Dropzone(el, {
            url: processUrl,
            paramName: 'file',
            acceptedFiles: el.dataset.accept || undefined,
            maxFilesize: maxSizeBytes ? maxSizeBytes / (1024 * 1024) : undefined,
            maxFiles: multiple ? null : 1,
            addRemoveLinks: true,
            dictDefaultMessage: 'Kéo thả hoặc bấm để chọn file',
            dictRemoveFile: 'Xóa',
            dictCancelUpload: 'Hủy',
            dictMaxFilesExceeded: 'Đã đạt số lượng file tối đa.',
            dictFileTooBig: 'File quá lớn ({{filesize}}MB). Tối đa {{maxFilesize}}MB.',
            dictInvalidFileType: 'Định dạng file không được hỗ trợ.',
            headers: { 'X-CSRF-TOKEN': csrf },
        });

        // Single mode: only one file may be present — replace instead of accumulate.
        dz.on('addedfile', (file) => {
            if (!multiple && dz.files.length > 1) {
                dz.removeFile(dz.files[0]);
            }
        });

        dz.on('success', (file, response) => {
            // UploadController returns plain-text token; Dropzone may leave it as
            // a string or (rarely) wrap it — always persist the token string.
            const token = typeof response === 'string'
                ? response.trim()
                : (response?.token ?? response?.id ?? '');
            if (!token) {
                console.error('[uploader] success without token', response);
                dz.emit('error', file, 'Phản hồi upload không hợp lệ');
                return;
            }
            file.uploadedToken = token;
            attachHiddenInput(file, token);
        });

        dz.on('error', (file, message) => {
            console.error('[uploader] upload error', message);
        });

        dz.on('removedfile', (file) => {
            file.hiddenInput?.remove();

            if (!multiple && dz.files.length === 0) {
                ensureEmptyPlaceholder();
            }

            // Only revert (delete the tmp staged file) for uploads that actually
            // reached the server — existing/committed files are cleaned up
            // server-side by prepareData() comparing old vs new paths on save.
            if (file.uploadedToken) {
                fetch(revertUrl, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'text/plain' },
                    body: file.uploadedToken,
                }).catch((err) => console.error('[uploader] revert error', err));
            }
        });

        // Existing files: Dropzone has no native "local file" concept — use the
        // documented mock-file pattern (emit the same events a real upload fires).
        existingPaths.forEach((path, i) => {
            const mockFile = { name: path.split('/').pop() || path, size: 0 };
            dz.emit('addedfile', mockFile);
            dz.emit('thumbnail', mockFile, existingUrls[i]);
            dz.emit('complete', mockFile);
            mockFile.status = Dropzone.SUCCESS;
            dz.files.push(mockFile);
            attachHiddenInput(mockFile, path);
        });

        if (existingPaths.length === 0) {
            ensureEmptyPlaceholder();
        }

        let dragInstance = null;
        if (multiple) {
            // distance: require a short drag before sortable starts so clicks on
            // "Xóa" / preview still work (same class of bug as table row actions).
            dragInstance = new Sortable([el], {
                draggable: '.dz-preview',
                distance: 8,
                mirror: { appendTo: 'body', constrainDimensions: true },
                classes: { mirror: 'shadow-lg', 'source:dragging': 'opacity-40' },
            });
            // See sortable.js: keep the <body>-appended mirror out of Alpine's reach.
            dragInstance.on('mirror:created', ({ mirror }) => mirror.setAttribute('x-ignore', ''));
        }

        DROPZONE_INSTANCES.set(el, { dz, dragInstance });
    },
    destroy(el) {
        const instances = DROPZONE_INSTANCES.get(el);
        instances?.dragInstance?.destroy();
        instances?.dz?.destroy();
        DROPZONE_INSTANCES.delete(el);
    },
});
