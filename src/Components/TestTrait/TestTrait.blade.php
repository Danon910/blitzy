declare(strict_types=1);

namespace Tests\{{ $namespace }};

@foreach($imports as $import)
use {{ $import }};
@endforeach

trait {{ $name }}Trait
{
@foreach($methods as $method)
    {{ $method }}
@if (!$loop->last)

@endif
@endforeach
}
