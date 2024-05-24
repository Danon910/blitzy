@if ($has_data)
    $response = $this->{{ $method }}Json(route("{{ $route }}"), $entry_data);
@else
    $response = $this->{{ $method }}Json(route("{{ $route }}"));
@endif
