@if (!empty($annotations))
/**
@foreach ($annotations as $annotation => $value)
@if (is_numeric($annotation))
    *
@else
    * #{{ $annotation }} {{ $value }}
@endif
@endforeach
    */
@endif
    public function {{ $name }}(): void
    {
@if (!empty($before_given))
@foreach ($before_given as $before_given_item)
        {{ $before_given_item }}
@endforeach

@endif
        // GIVEN
@foreach ($given as $given_item)
        {{ $given_item }}
@endforeach

        // WHEN
@foreach ($when as $when_item)
        {{ $when_item }}
@endforeach

        // THEN
@foreach ($then as $then_item)
        {{ $then_item }}
@endforeach
    }
