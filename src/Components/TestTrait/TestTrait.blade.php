declare(strict_types=1);

namespace Tests\{{ $namespace }};

trait {{ $name }}Trait
{
@foreach($methods as $method)
    {{ $method }}
@if (!$loop->last)

@endif
@endforeach
}
