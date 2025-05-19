<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $label }}</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="input-group">
                <button type="button" class="btn btn-secondary btn-add-textarea-{{ $name }}">Thêm</button>
            </div>
        </div>

        <div id="textarea-container-{{ $name }}" class="mt-2">
            <ul class="list-group" id="list-{{$name}}">
                @if (is_array($value) && count($value) > 0)
                    @foreach ($value as $index => $item)
                        <li class="list-group-item d-flex align-items-start flex-column">
                            <div class="d-flex align-items-center mb-2 w-100">
                                <span class="mr-2 textarea-index">{{ $index + 1 }}.</span>
                                <textarea class="form-control flex-grow-1 w-100" name="{{ $name }}[]" rows="3"
                                          placeholder="Enter {{ $label }}">{{ $item }}</textarea>
                            </div>
                            <div class="mt-2 d-flex justify-content-end w-100">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-textarea-{{$name}}"
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
            let list_{{$name}} = document.getElementById("list-{{$name}}");
            let buttonAdd_{{$name}} = document.querySelector(".btn-add-textarea-{{$name}}");
            let textareaCount = {{ is_array($value) ? count($value) : 0 }};


            buttonAdd_{{$name}}.addEventListener('click', function () {
                let li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'align-items-start', 'flex-column');
                li.innerHTML = `<div class="d-flex align-items-center mb-2 w-100">
                                        <span class="mr-2 textarea-index">${textareaCount + 1}.</span>
                                            <textarea class="form-control flex-grow-1 w-100" name="{{ $name }}[]" rows="3" placeholder="Enter {{ $label }}"></textarea>
                                   </div>
                                 <div class="mt-2 d-flex justify-content-end w-100">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-textarea-{{$name}}" data-index="${textareaCount}">Xoá</button>
                                   </div>`;
                list_{{$name}}.appendChild(li);
                addRemoveEvent(li.querySelector('.btn-remove-textarea-{{$name}}'));
                textareaCount++;
                updateTextareaIndexes();
            });

            function addRemoveEvent(removeButton) {
                removeButton.addEventListener('click', function () {
                    removeButton.closest('li').remove();
                    updateTextareaIndexes();
                });
            }

            function updateTextareaIndexes() {
                list_{{$name}}.querySelectorAll('.list-group-item').forEach((li, index) => {
                    li.querySelector('.textarea-index').textContent = `${index + 1}.`
                })
            }

            list_{{$name}}.querySelectorAll('.btn-remove-textarea-{{$name}}').forEach(button => {
                addRemoveEvent(button)
            });
        });
    </script>
@endpush
