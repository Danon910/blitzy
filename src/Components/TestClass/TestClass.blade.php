declare(strict_types=1);

namespace Tests\{{ $namespace }};

@foreach($imports as $import)
use {{ $import }};
@endforeach

class {{ $name }}Test extends TestCase
{
@foreach($traits as $trait)
    use {{ $trait }};
@endforeach

@foreach($methods as $method)
    {{ $method }}
@if (!$loop->last)

@endif
@endforeach
}
