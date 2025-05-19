<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$label}}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="input-{{$name}}">{{$label}}</label>
            <div class="input-group">
                <input type="text" class="form-control" id="input-{{$name}}" name="{{$name}}"
                       placeholder="Enter {{$label}}">
                <div class="input-group-append">
                    <button type="button" class="btn btn-secondary btn-add-{{$name}}">Thêm</button>
                </div>
            </div>
        </div>
        <ul id="list-{{ $name }}" class="list-group">
            @if (is_array($value) && count($value) > 0)
                @foreach ($value as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <input type="text" class="form-control flex-grow-1 mr-2" name="{{ $name }}[]"
                               value="{{ $item }}">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-{{$name}}"
                                data-value="{{ $item }}">Xoá
                        </button>
                    </li>
                @endforeach
            @endif
        </ul>
        @error($name)
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

@push("scripts")
    <script>
        let input_{{$name}} = document.getElementById("input-{{$name}}");
        let list_{{$name}} = document.getElementById("list-{{$name}}");
        let button_add_{{$name}} = document.querySelector(".btn-add-{{$name}}");

        button_add_{{$name}}.addEventListener('click', function () {
            let value = input_{{$name}}.value.trim();
            if (value !== "") {
                let li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                li.innerHTML = //html
                    `<input type="text" class="form-control flex-grow-1 mr-2" name="{{$name}}[]" value="${value}">
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-{{$name}}" data-value="${value}">Xoá</button>`;
                list_{{$name}}.appendChild(li);
                input_{{$name}}.value = "";
                // Thêm sự kiện xóa mới cho nút xóa vừa tạo
                addRemoveEvent(li.querySelector('.btn-remove-{{$name}}'));
            }
        });

        function addRemoveEvent(removeButton) {
            removeButton.addEventListener('click', function () {
                removeButton.closest('li').remove();
            });
        }

        list_{{$name}}.querySelectorAll('.btn-remove-{{$name}}').forEach(button => {
            addRemoveEvent(button)
        });
    </script>
@endpush
