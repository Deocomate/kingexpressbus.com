{{-- Admin JS is served as static ESM from public/js/admin.js (not bundled by Vite).
     Bare specifiers below resolve via this import map to pinned CDN builds (jsDelivr +esm).
     Tiptap packages use esm.sh with an explicit ?deps= pin instead: jsDelivr +esm resolves
     each package's own transitive @tiptap/core/@tiptap/pm dependency independently, which
     produced TWO different @tiptap/core versions loaded side by side and ProseMirror's
     "Adding different instances of a keyed plugin" crash. esm.sh's ?deps= forces every
     Tiptap package below to share the exact same @tiptap/core + @tiptap/pm module instance.
     No SRI on import map entries: the spec does not support an integrity field for them. --}}
<script type="importmap">
{
    "imports": {
        "alpinejs": "https://cdn.jsdelivr.net/npm/alpinejs@3.15.12/+esm",
        "@alpinejs/focus": "https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.14.8/+esm",
        "flatpickr": "https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/+esm",
        "flatpickr/dist/l10n/vn.js": "https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/vn.js/+esm",
        "autonumeric": "https://cdn.jsdelivr.net/npm/autonumeric@4.10.10/+esm",
        "nouislider": "https://cdn.jsdelivr.net/npm/nouislider@15.8.1/+esm",
        "tom-select": "https://cdn.jsdelivr.net/npm/tom-select@2.6.2/+esm",
        "chart.js": "https://cdn.jsdelivr.net/npm/chart.js@4.5.1/+esm",
        "sweetalert2": "https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/+esm",
        "@shopify/draggable": "https://cdn.jsdelivr.net/npm/@shopify/draggable@1.2.1/+esm",
        "@tiptap/core": "https://esm.sh/@tiptap/core@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/starter-kit": "https://esm.sh/@tiptap/starter-kit@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-underline": "https://esm.sh/@tiptap/extension-underline@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-subscript": "https://esm.sh/@tiptap/extension-subscript@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-superscript": "https://esm.sh/@tiptap/extension-superscript@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-link": "https://esm.sh/@tiptap/extension-link@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-text-align": "https://esm.sh/@tiptap/extension-text-align@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-table": "https://esm.sh/@tiptap/extension-table@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-table-row": "https://esm.sh/@tiptap/extension-table-row@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-table-cell": "https://esm.sh/@tiptap/extension-table-cell@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1",
        "@tiptap/extension-table-header": "https://esm.sh/@tiptap/extension-table-header@2.27.2?deps=@tiptap/core@2.27.2,@tiptap/pm@2.27.1"
    }
}
</script>

{{-- Third-party widget CSS (no longer bundled via Vite/PostCSS import) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.8.1/dist/nouislider.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.6.2/dist/css/tom-select.default.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.css">

{{-- Dropzone 5.x is UMD-only (no reliable ESM build) — load classic before the admin.js module
     so window.Dropzone exists when uploader.js runs. autoDiscover MUST be disabled
     immediately: it matches class="dropzone" (required for CDN CSS), attaches without
     a URL, then leaves element.dropzone set so our registry init throws
     "Dropzone already attached" and never wires upload/hidden-input handlers. --}}
<script
    src="https://cdn.jsdelivr.net/npm/dropzone@5.9.3/dist/min/dropzone.min.js"
    integrity="sha384-PwiT+fWTPpIySx6DrH1FKraKo+LvVpOClsjx0TSdMYTKi7BR1hR149f4VHLUUnfA"
    crossorigin="anonymous"
></script>
<script>
    window.Dropzone && (window.Dropzone.autoDiscover = false);
</script>

<script type="module" src="{{ asset('js/admin.js') }}"></script>
