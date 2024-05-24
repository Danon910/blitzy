private function create{{ $model }}(): {{ $model }}
{
    return {{ $model }}::factory()->create();
}
