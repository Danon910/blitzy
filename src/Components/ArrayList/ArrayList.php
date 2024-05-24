<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\ArrayList;

use Danon910\blitzy\Components\BaseComponent;

class ArrayList extends BaseComponent
{
    public function __construct(
        private readonly array $properties,
    )
    {
    }

    public static function make(
        array $properties,
    ): self
    {
        $parsed_properties = [];

        foreach ($properties as $property => $value) {
            if (is_null($value)) {
                $parsed_properties[$property] = null;
            } else {
                $parsed_properties[$property] = is_numeric($value) ? $value : '"' . $value . '"';
            }
        }

        return new self($parsed_properties);
    }

    public function getAttributes(): array
    {
        return [
            'properties' => $this->properties,
        ];
    }
}
