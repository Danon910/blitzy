/** @var {{ $class_name }} ${{ $variable_name }} */
@if (count($properties) > 0)
    ${{ $variable_name }} = app()->make({{ $class_name }}::class, [
    @foreach ($properties as $key => $property)
        "{{ $key }}" => "{{ $property }}",
    @endforeach
    ]);
@else
        ${{ $variable_name }} = app()->make({{ $class_name }}::class);
@endif
