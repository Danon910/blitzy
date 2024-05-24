<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\RequestJson;

use Danon910\blitzy\Components\BaseComponent;

class RequestJson extends BaseComponent
{
    private static bool $has_data = false;

    public function __construct(
        private readonly string $method,
        private readonly string $route,
    )
    {
    }

    public static function make(
        string $method,
        string $route,
    ): self
    {
        if (in_array(mb_strtolower($method), ['post', 'put', 'delete'])) {
            self::$has_data = true;
        }

        return new self($method, $route);
    }

    public function getAttributes(): array
    {
        return [
            'method' => strtolower($this->method),
            'route' => $this->route,
            'has_data' => self::$has_data,
        ];
    }

    public function hasData(): bool
    {
        return self::$has_data;
    }
}
