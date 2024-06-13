@if (empty($parameters))
    {{ $visibility }} function {{ $name }}(): {{ $type }}
    {
        {{ $content }}
    }
@else
    {{ $visibility }} function {{ $name }}(
    @foreach ($parameters as $var => $parameter)
    {{ $var }} ${{ $parameter }},
    @endforeach
): {{ $type }}
    {
        {{ $content }}
    }
@endif
