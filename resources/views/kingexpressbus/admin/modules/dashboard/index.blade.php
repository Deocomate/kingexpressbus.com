@extends("kingexpressbus.admin.layouts.main")
@section("title","Dashboard")
@section("content")
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Element</h3>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="alert alert-primary">Đây là các input component có thể sử dụng trong form</div>
                @csrf
                <x-inputs.text label="Input Text" name="text"></x-inputs.text>
                <x-inputs.email label="Input Email" name="email"></x-inputs.email>
                <x-inputs.number label="Input Number" name="number"></x-inputs.number>
                <x-inputs.text-area label="Input Text Area" name="text-area"></x-inputs.text-area>
                <x-inputs.select label="Input Select 2" name="select-2">
                    <option value="1" @selected(true)>Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                    <option value="4">Option 4</option>
                    <option value="5">Option 5</option>
                </x-inputs.select>
                <x-inputs.select-multiple label="Input Select Multiple" name="select-multiple">
                    <option value="1" selected>Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3" selected>Option 3</option>
                    <option value="4">Option 4</option>
                    <option value="5">Option 5</option>
                </x-inputs.select-multiple>
                <x-inputs.date label="Input Date" name="date"></x-inputs.date>
                <x-inputs.time label="Input Time" name="time"></x-inputs.time>
                <x-inputs.editor label="Input Editor" name="editor"></x-inputs.editor>
                <!-- Input Image Link trả về đường dẫn của hình ảnh được lưu trong ckfinder -->
                <x-inputs.image-link label="Input Image Link" name="image"></x-inputs.image-link>

                <div class="alert alert-primary">Các phần tử nâng cao</div>
                <x-inputs.text-array label="Text Array" name="text_array"></x-inputs.text-array>
                <x-inputs.image-link-array label="Image Link Array" name="image_link_array"></x-inputs.image-link-array>
                <x-inputs.editor-array label="Editor Array" name="editor_array"></x-inputs.editor-array>
                <x-inputs.text-area-array label="Text Area Array" name="text_area_array"></x-inputs.text-area-array>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection
