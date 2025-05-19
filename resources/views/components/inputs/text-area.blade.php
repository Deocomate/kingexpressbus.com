<div class="form-group">
    <label for="input-{{$name}}">{{$label}}</label>
    <textarea class="form-control" id="input-{{$name}}" name="{{$name}}" required
              rows="3" placeholder="Enter {{$label}}">{{$value ?: old($name)}}</textarea>
    @error($name)
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
