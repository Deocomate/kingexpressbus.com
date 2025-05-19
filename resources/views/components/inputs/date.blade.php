<div class="form-group">
    <label for="input-{{$name}}">{{$label}}</label>
    <input type="date" id="input-{{$name}}" name="{{$name}}" class="form-control" required
           value="{{ $value ?: old($name) }}"
           onfocus="this.showPicker()">
    @error($name)
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

@if($value=="" && old($name) == "")
    @push('scripts')
        <script>
            document.getElementById('input-{{$name}}').value = (new Date()).toISOString().substring(0, 10)
        </script>
    @endpush
@endif
