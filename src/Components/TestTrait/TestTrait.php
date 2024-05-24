<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\TestTrait;

use Illuminate\Support\Str;
use Danon910\blitzy\Components\BaseComponent;

class TestTrait extends BaseComponent
{
    public function __construct(
        private readonly string $namespace,
        private readonly string $name,
        private readonly array $methods,
    )
    {
    }

    public static function make(
        string $namespace,
        string $name,
        array $methods,
    ): self
    {
        return new self($namespace, $name, $methods);
    }

    public function getAttributes(): array
    {
        return [
            'namespace' => Str::studly($this->namespace),
            'name' => Str::studly($this->name),
            'methods' => $this->methods,
        ];
    }
}
