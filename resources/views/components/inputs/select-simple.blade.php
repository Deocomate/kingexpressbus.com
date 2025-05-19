<div class="form-group">
    <label for="input-{{$name}}">{{$label}}</label>
    <select name="{{$name}}" id="input-{{$name}}" class="form-control" required>
        {{$slot}}
    </select>
    @error($name)
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
