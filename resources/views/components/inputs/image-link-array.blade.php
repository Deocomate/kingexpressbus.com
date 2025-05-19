<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$label}}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="input-{{$name}}">{{$label}}</label>
            <div class="input-group">
                <input readonly type="text" class="form-control ckfinder-input-{{$name}}" id="input-{{ $name }}"
                       placeholder="Chọn ảnh">
                <span class="input-group-append">
                    <button type="button" class="btn btn-secondary ckfinder-popup-{{$name}}"
                            id="button-popup-{{$name}}">
                        Duyệt Ảnh
                    </button>
                </span>
            </div>
        </div>

        <div id="preview-container-{{ $name }}" class="mt-2">
            <ul class="list-group" id="list-{{$name}}">
                @if (is_array($value) && count($value) > 0)
                    @foreach ($value as $item)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <img src="{{ $item }}" alt="Image Preview"
                                 style="max-width: 100px; max-height: 100px; margin-right: 10px;">
                            <input type="hidden" name="{{ $name }}[]" value="{{ $item }}">
                            <button type="button" class="btn btn-danger btn-sm btn-remove-image-{{$name}}"
                                    data-value="{{ $item }}">Xoá
                            </button>
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
        let button_popup_{{$name}} = document.getElementById("button-popup-{{$name}}");
        let list_{{$name}} = document.getElementById("list-{{$name}}");
        let input_{{$name}} = document.querySelector(".ckfinder-input-{{$name}}");

        button_popup_{{$name}}.onclick = async () => {
            CKFinder.popup({
                chooseFiles: true,
                width: 800,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        let file = evt.data.files.first();
                        let fullUrl = file.getUrl();
                        let path;
                        try {
                            let urlObj = new URL(fullUrl);
                            path = urlObj.pathname;
                        } catch (e) {
                            path = fullUrl;
                        }

                        let li = document.createElement('li');
                        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                        li.innerHTML = `<img src="${path}" alt="Image Preview" style="max-width: 100px; max-height: 100px; margin-right: 10px;">
                                                    <input type="hidden" name="{{ $name }}[]" value="${path}">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove-image-{{$name}}" data-value="${path}">Xoá</button>`;
                        list_{{$name}}.appendChild(li);
                        input_{{$name}}.value = "";
                        addRemoveEvent(li.querySelector('.btn-remove-image-{{$name}}'));
                    });
                    finder.on('file:choose:resizedImage', function (evt) {
                    });
                }
            });
        }

        function addRemoveEvent(removeButton) {
            removeButton.addEventListener('click', function () {
                removeButton.closest('li').remove();
            });
        }

        // Gán sự kiện xóa cho các nút xóa đã có từ trước
        list_{{$name}}.querySelectorAll('.btn-remove-image-{{$name}}').forEach(button => {
            addRemoveEvent(button)
        });
    </script>
@endpush
