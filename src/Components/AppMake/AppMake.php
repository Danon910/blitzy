<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\AppMake;

use Danon910\blitzy\Components\BaseComponent;

class AppMake extends BaseComponent
{
    private array $properties = [];

    public function __construct(
        private readonly string $variable_name,
        private readonly string $class_name,
    )
    {
    }

    public static function make(
        string $variable_name,
        string $class_name,
    ): self
    {
        return new self($variable_name, $class_name);
    }

    public function addProperty($property): void
    {
        $this->properties = array_merge($this->properties, $property);
    }

    public function getAttributes(): array
    {
        return [
            'variable_name' => $this->variable_name,
            'class_name' => $this->class_name,
            'properties' => $this->properties,
        ];
    }
}
