return [
    @foreach ($properties as $key => $value)
    @if ($value != null)
    "{{ $key }}" => {{ $value }},
    @else
"{{ $key }}",
    @endif
    @endforeach
];
