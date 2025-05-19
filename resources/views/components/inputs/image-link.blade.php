<div class="form-group">
    <label for="input-{{$name}}">{{$label}}</label>
    <div class="input-group">
        <input readonly type="text" class="form-control ckfinder-input" name="{{ $name }}" id="input-{{ $name }}"
               required value="{{$value==""?old($name):$value}}">
        <span class="input-group-append">
            <button type="button" class="btn btn-secondary ckfinder-popup" id="button-popup-{{$name}}">
                Duyệt Ảnh
            </button>
        </span>
    </div>
    @error($name)
    <div class="text-danger">{{ $message }}</div>
    @enderror

    <div id="preview-container-{{ $name }}" style="margin-top: 10px;">
        @if($value || old($name))
            <img src="{{ $value ?: old($name) }}" alt="Image Preview" id="preview-image-{{ $name }}"
                 style="max-width: 200px; max-height: 200px; display: block;">
        @else
            <img src="" alt="Image Preview" id="preview-image-{{ $name }}"
                 style="max-width: 200px; max-height: 200px; display: none;">
        @endif
    </div>
</div>

@push("scripts")
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let button_popup_{{$name}} = document.getElementById("button-popup-{{$name}}");
            let preview_container_{{$name}} = document.getElementById("preview-container-{{$name}}");
            let preview_image_{{$name}} = document.getElementById("preview-image-{{$name}}");

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
                            let parentElement = button_popup_{{$name}}.closest(".input-group");
                            if (parentElement) {
                                let input = parentElement.querySelector(".ckfinder-input");
                                if (input) {
                                    input.value = path;
                                }
                            }

                            if (preview_image_{{$name}}) {
                                preview_image_{{$name}}.src = path;
                                preview_image_{{$name}}.style.display = "block";
                            }
                            if (preview_container_{{$name}}) {
                                preview_container_{{$name}}.style.display = "block";
                            }
                        });
                        finder.on('file:choose:resizedImage', function (evt) {
                        });
                    }
                });
            }
        });
    </script>
@endpush
