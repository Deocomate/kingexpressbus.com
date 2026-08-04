@props(['items' => [], 'columns' => 2])
<dl class="grid grid-cols-{{ $columns }} gap-x-6 gap-y-3">
    @foreach($items as $label => $value)
    <div>
        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</dt>
        <dd class="mt-0.5 text-sm text-gray-900">
            @if($value instanceof \Illuminate\Support\HtmlString)
                {!! $value !!}
            @else
                {{ $value ?? '—' }}
            @endif
        </dd>
    </div>
    @endforeach
    {{ $slot ?? '' }}
</dl>
