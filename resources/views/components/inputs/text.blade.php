<div class="form-group">
    <label for="input-{{$name}}">{{$label}}</label>
    <input type="text" class="form-control" id="input-{{$name}}" name="{{$name}}"
           value="{{ $value ?: old($name) }}" placeholder="Enter {{$label}}" required>
    @error($name)
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
