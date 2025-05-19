<div class="form-group">
    <label for="input-{{$name}}">{{$label}}</label>
    <select name="{{$name}}" class="form-control" id="input-{{$name}}" style="width: 100%;"
            required {{$attributes}}>
        {{$slot}}
    </select>
    @error($name)
    <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

@pushonce('styles')
    <!-- Select 2 Style -->
    <link rel="stylesheet" href="{{asset("/admin/plugins/select2/css/select2.min.css")}}">
    <link rel="stylesheet" href="{{asset("/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css")}}">
@endpushonce

@pushonce('scripts')
    <!-- Select 2 Script -->
    <script src="{{asset("/admin/plugins/select2/js/select2.full.min.js")}}"></script>
@endpushonce

@push('scripts')
    <script>
        $('#input-{{$name}}').select2({
            theme: "bootstrap4"
        });
    </script>
@endpush
