<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\RequestJson;

use Danon910\blitzy\Enums\HttpMethod;
use Danon910\blitzy\Components\BaseComponent;

class RequestJson extends BaseComponent
{
    private static bool $has_data = false;

    public function __construct(
        private readonly HttpMethod $method,
        private readonly string $route,
    )
    {
        if (in_array($method, [HttpMethod::POST, HttpMethod::PUT, HttpMethod::DELETE])) {
            self::$has_data = true;
        }
    }

    public static function make(
        string $method,
        string $route,
    ): self
    {
        $method = mb_strtolower($method);
        $method = HttpMethod::from($method);

        return new self($method, $route);
    }

    public function getAttributes(): array
    {
        return [
            'method' => $this->method->value,
            'route' => $this->route,
            'has_data' => self::$has_data,
        ];
    }

    public function hasData(): bool
    {
        return self::$has_data;
    }
}
