<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\VariableValue;

use Danon910\blitzy\Components\BaseComponent;

class VariableValue extends BaseComponent
{
    public function __construct(
        private readonly string $name,
        private readonly string $value,
    )
    {
    }

    public static function make(
        string $name,
        string $value,
    ): self
    {
        return new self($name, $value);
    }

    public function getAttributes(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }
}
