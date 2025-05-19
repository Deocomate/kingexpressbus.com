<div class="form-group">
    <label for="input-{{$name}}">{{$label}}</label>
    <input type="time" class="form-control" id="input-{{$name}}" name="{{$name}}"
           value="{{$value ?: old($name)}}" required
           onfocus="this.showPicker()">
    @error($name)
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

@if($value=="" && old($name) == "")
    @push('scripts')
        <script>
            document.getElementById('input-{{$name}}').value = (new Date()).toTimeString().substring(0, 5);
        </script>
    @endpush
@endif

