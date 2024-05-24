@if (empty($parameters))
    {{ $visibility }} function {{ $name }}(): {{ $type }}
    {
        {{ $content }}
    }
@else
    {{ $visibility }} function {{ $name }}(
    @foreach ($parameters as $parameter)
        {{ $parameter }}
    @endforeach
): {{ $type }}
    {
        {{ $content }}
    }
@endif
