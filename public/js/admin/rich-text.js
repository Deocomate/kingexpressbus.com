/**
 * Tiptap v2 rich text editor.
 * Dynamic import to keep initial bundle smaller.
 * Syncs content to hidden textarea on every update for form submission.
 */

import { register } from './registry.js';

const EDITOR_INSTANCES = new WeakMap();

register('[data-rich-text]', {
    async init(el) {
        const [
            { Editor },
            { StarterKit },
            { Underline },
            { Subscript },
            { Superscript },
            { Link },
            { TextAlign },
            { Table },
            { TableRow },
            { TableCell },
            { TableHeader },
        ] = await Promise.all([
            import('@tiptap/core'),
            import('@tiptap/starter-kit'),
            import('@tiptap/extension-underline'),
            import('@tiptap/extension-subscript'),
            import('@tiptap/extension-superscript'),
            import('@tiptap/extension-link'),
            import('@tiptap/extension-text-align'),
            import('@tiptap/extension-table'),
            import('@tiptap/extension-table-row'),
            import('@tiptap/extension-table-cell'),
            import('@tiptap/extension-table-header'),
        ]);

        const name = el.dataset.name;
        const uploadUrl = el.dataset.uploadUrl;
        const textarea = el.parentElement?.querySelector(`textarea[name="${name}"]`);
        const initialContent = textarea?.value || '';

        // Build toolbar
        const toolbar = buildToolbar(el, uploadUrl);
        el.insertBefore(toolbar, el.firstChild);

        // Editor content area
        const contentArea = document.createElement('div');
        contentArea.classList.add('p-3', 'min-h-[200px]', 'focus-within:outline-none');
        el.appendChild(contentArea);

        const editor = new Editor({
            element: contentArea,
            extensions: [
                StarterKit.configure({ history: true }),
                Underline,
                Subscript,
                Superscript,
                Link.configure({ openOnClick: false }),
                TextAlign.configure({ types: ['heading', 'paragraph'] }),
                Table.configure({ resizable: false }),
                TableRow,
                TableCell,
                TableHeader,
            ],
            content: initialContent,
            onUpdate({ editor: e }) {
                if (textarea) {
                    textarea.value = e.getHTML();
                }
            },
        });

        // Sync initial
        if (textarea) textarea.value = editor.getHTML();

        // Wire toolbar buttons
        wireToolbar(toolbar, editor);

        EDITOR_INSTANCES.set(el, editor);
    },
    destroy(el) {
        EDITOR_INSTANCES.get(el)?.destroy();
        EDITOR_INSTANCES.delete(el);
        // Remove injected toolbar
        el.querySelector('[data-rich-toolbar]')?.remove();
        el.querySelector('.ProseMirror')?.parentElement?.remove();
    },
});

function buildToolbar(el, uploadUrl) {
    const bar = document.createElement('div');
    bar.dataset.richToolbar = '1';
    bar.className = 'flex flex-wrap gap-1 p-2 border-b border-gray-200 bg-gray-50';

    const buttons = [
        { cmd: 'bold', icon: '<b>B</b>', title: 'Đậm' },
        { cmd: 'italic', icon: '<i>I</i>', title: 'Nghiêng' },
        { cmd: 'underline', icon: '<u>U</u>', title: 'Gạch dưới' },
        { cmd: 'strike', icon: '<s>S</s>', title: 'Gạch ngang' },
        { cmd: 'subscript', icon: 'x<sub>2</sub>', title: 'Chỉ số dưới' },
        { cmd: 'superscript', icon: 'x<sup>2</sup>', title: 'Chỉ số trên' },
        { separator: true },
        { cmd: 'heading2', icon: 'H2', title: 'Tiêu đề 2' },
        { cmd: 'heading3', icon: 'H3', title: 'Tiêu đề 3' },
        { separator: true },
        { cmd: 'alignLeft', icon: '⬅', title: 'Trái' },
        { cmd: 'alignCenter', icon: '↔', title: 'Giữa' },
        { cmd: 'alignRight', icon: '➡', title: 'Phải' },
        { separator: true },
        { cmd: 'blockquote', icon: '❝', title: 'Trích dẫn' },
        { cmd: 'codeBlock', icon: '</>', title: 'Code block' },
        { cmd: 'bulletList', icon: '• List', title: 'Danh sách không số' },
        { cmd: 'orderedList', icon: '1. List', title: 'Danh sách có số' },
        { separator: true },
        { cmd: 'table', icon: '⊞', title: 'Bảng' },
        { separator: true },
        { cmd: 'undo', icon: '↩', title: 'Hoàn tác' },
        { cmd: 'redo', icon: '↪', title: 'Làm lại' },
    ];

    buttons.forEach(({ cmd, icon, title, separator }) => {
        if (separator) {
            const sep = document.createElement('span');
            sep.className = 'w-px bg-gray-300 mx-0.5 self-stretch';
            bar.appendChild(sep);
            return;
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerHTML = icon;
        btn.title = title;
        btn.dataset.toolbarCmd = cmd;
        btn.className = 'px-2 py-1 text-sm rounded hover:bg-gray-200 text-gray-700 transition-colors';
        bar.appendChild(btn);
    });

    return bar;
}

function wireToolbar(toolbar, editor) {
    toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-toolbar-cmd]');
        if (!btn) return;
        e.preventDefault();
        const cmd = btn.dataset.toolbarCmd;

        const chain = editor.chain().focus();
        switch (cmd) {
            case 'bold': chain.toggleBold().run(); break;
            case 'italic': chain.toggleItalic().run(); break;
            case 'underline': chain.toggleUnderline().run(); break;
            case 'strike': chain.toggleStrike().run(); break;
            case 'subscript': chain.toggleSubscript().run(); break;
            case 'superscript': chain.toggleSuperscript().run(); break;
            case 'heading2': chain.toggleHeading({ level: 2 }).run(); break;
            case 'heading3': chain.toggleHeading({ level: 3 }).run(); break;
            case 'alignLeft': chain.setTextAlign('left').run(); break;
            case 'alignCenter': chain.setTextAlign('center').run(); break;
            case 'alignRight': chain.setTextAlign('right').run(); break;
            case 'blockquote': chain.toggleBlockquote().run(); break;
            case 'codeBlock': chain.toggleCodeBlock().run(); break;
            case 'bulletList': chain.toggleBulletList().run(); break;
            case 'orderedList': chain.toggleOrderedList().run(); break;
            case 'table': chain.insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(); break;
            case 'undo': chain.undo().run(); break;
            case 'redo': chain.redo().run(); break;
        }
    });
}
