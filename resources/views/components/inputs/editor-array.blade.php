<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $label }}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            {{-- <label for="input-{{ $name }}">{{ $label }}</label> --}}
            <div class="input-group">
                <button type="button" class="btn btn-secondary btn-add-editor-{{ $name }}">Thêm</button>
            </div>
        </div>

        <div id="editor-container-{{ $name }}" class="mt-2">
            <ul class="list-group" id="list-{{$name}}">
                @if (is_array($value) && count($value) > 0)
                    @foreach ($value as $index => $item)
                        <li class="list-group-item d-flex align-items-start flex-column">
                            <textarea name="{{ $name }}[]" id="editor-{{ $name }}-{{ $index }}"
                                      class="form-control ckeditor-textarea">{!! $item !!}</textarea>
                            <div class="mt-2 d-flex justify-content-end w-100">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-editor-{{$name}}"
                                        data-index="{{ $index }}">Xoá
                                </button>
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>

        @error($name)
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

@push("scripts")
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let editorContainer_{{$name}} = document.getElementById("editor-container-{{$name}}");
            let list_{{$name}} = document.getElementById("list-{{$name}}");
            let buttonAdd_{{$name}} = document.querySelector(".btn-add-editor-{{$name}}");
            let editorCount = {{ is_array($value) ? count($value) : 0 }};
            let ckeditorInstances = {};


            buttonAdd_{{$name}}.addEventListener('click', function () {
                let li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'align-items-start', 'flex-column');
                let editorId = `editor-{{ $name }}-${editorCount}`;
                li.innerHTML = `<textarea name="{{ $name }}[]" id="${editorId}" class="form-control ckeditor-textarea"></textarea>
                                  <div class="mt-2 d-flex justify-content-end w-100">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-editor-{{$name}}" data-index="${editorCount}">Xoá</button>
                                    </div>`;
                list_{{$name}}.appendChild(li);
                initCkEditor(editorId);
                addRemoveEvent(li.querySelector('.btn-remove-editor-{{$name}}'));
                editorCount++;
            });


            function initCkEditor(editorId) {
                if (ckeditorInstances[editorId]) {
                    return; // Skip if CKEditor instance already exists
                }

                CKEDITOR.ClassicEditor.create(document.getElementById(editorId), {
                    toolbar: {
                        items: [
                            'findAndReplace', 'selectAll',
                            '|',
                            'heading',
                            '|',
                            'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript',
                            'bulletedList', 'numberedList', 'todoList',
                            '|',
                            'outdent', 'indent', '|',
                            'undo', 'redo',
                            '-',
                            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight',
                            '|',
                            'alignment',
                            '|',
                            'link', 'insertImage', "CKFinder", 'blockQuote', 'insertTable', 'mediaEmbed',
                            'htmlEmbed',
                            '|',
                            'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                            'sourceEditing'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    list: {
                        properties: {
                            styles: true,
                            startIndex: true,
                            reversed: true
                        }
                    },
                    ckfinder: {
                        openerMethod: 'popup',
                        options: {
                            resourceType: 'Images'
                        }
                    },
                    heading: {
                        options: [{
                            model: 'paragraph',
                            view: 'p',
                            title: 'Paragraph',
                            class: 'ck-heading_paragraph'
                        },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3'
                            },
                            {
                                model: 'heading4',
                                view: 'h4',
                                title: 'Heading 4',
                                class: 'ck-heading_heading4'
                            },
                            {
                                model: 'heading5',
                                view: 'h5',
                                title: 'Heading 5',
                                class: 'ck-heading_heading5'
                            },
                            {
                                model: 'heading6',
                                view: 'h6',
                                title: 'Heading 6',
                                class: 'ck-heading_heading6'
                            }
                        ]
                    },
                    placeholder: 'Content...',
                    fontFamily: {
                        options: [
                            'default',
                            'Arial, Helvetica, sans-serif',
                            'Courier New, Courier, monospace',
                            'Georgia, serif',
                            'Lucida Sans Unicode, Lucida Grande, sans-serif',
                            'Tahoma, Geneva, sans-serif',
                            'Times New Roman, Times, serif',
                            'Trebuchet MS, Helvetica, sans-serif',
                            'Verdana, Geneva, sans-serif'
                        ],
                        supportAllValues: true
                    },
                    fontSize: {
                        options: [10, 12, 14, 'default', 18, 20, 22],
                        supportAllValues: true
                    },
                    htmlSupport: {
                        allow: [{
                            name: /.*/,
                            attributes: true,
                            classes: true,
                            styles: true
                        }]
                    },
                    htmlEmbed: {
                        showPreviews: true
                    },
                    link: {
                        decorators: {
                            addTargetToExternalLinks: true,
                            defaultProtocol: 'https://',
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file'
                                }
                            }
                        }
                    },
                    mention: {
                        feeds: [{
                            marker: '@',
                            feed: [
                                '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes',
                                '@chocolate', '@cookie', '@cotton', '@cream',
                                '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread',
                                '@gummi', '@ice', '@jelly-o',
                                '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding',
                                '@sesame', '@snaps', '@soufflé',
                                '@sugar', '@sweet', '@topping', '@wafer'
                            ],
                            minimumCharacters: 1
                        }]
                    },
                    removePlugins: [
                        'EasyImage',
                        'RealTimeCollaborativeComments',
                        'RealTimeCollaborativeTrackChanges',
                        'RealTimeCollaborativeRevisionHistory',
                        'PresenceList',
                        'Comments',
                        'TrackChanges',
                        'TrackChangesData',
                        'RevisionHistory',
                        'Pagination',
                        'WProofreader',
                        'MathType'
                    ]
                }).then(editor => {
                    ckeditorInstances[editorId] = editor;
                })
            }


            function addRemoveEvent(removeButton) {
                removeButton.addEventListener('click', function () {
                    let li = removeButton.closest('li');
                    let textarea = li.querySelector('.ckeditor-textarea');
                    let editorId = textarea.id;
                    if (ckeditorInstances[editorId]) {
                        ckeditorInstances[editorId].destroy();
                        delete ckeditorInstances[editorId];
                    }
                    li.remove();
                });
            }

            //Khởi tạo ckeditor có sẵn
            list_{{$name}}.querySelectorAll('.ckeditor-textarea').forEach((textarea, index) => {
                initCkEditor(textarea.id)
            })
            list_{{$name}}.querySelectorAll('.btn-remove-editor-{{$name}}').forEach(button => {
                addRemoveEvent(button)
            });

        });
    </script>
@endpush
