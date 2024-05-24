<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\Method;

use Danon910\blitzy\Enums\MethodVisibility;
use Danon910\blitzy\Components\BaseComponent;

class Method extends BaseComponent
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly string $content,
        private readonly MethodVisibility $visibility,
        private readonly array $parameters,
    )
    {
    }

    public static function make(
        string $name,
        string $type,
        string $content,
        MethodVisibility $visibility = MethodVisibility::PRIVATE,
        array $parameters = [],
    ): self
    {
        return new self($name, $type, $content, $visibility, $parameters);
    }

    public function getAttributes(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'content' => $this->content,
            'visibility' => $this->visibility->value,
            'parameters' => $this->parameters,
        ];
    }
}
