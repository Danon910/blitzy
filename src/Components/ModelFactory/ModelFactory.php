<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\ModelFactory;

use Danon910\blitzy\Components\BaseComponent;

class ModelFactory extends BaseComponent
{
    public function __construct(
        private readonly string $model_name,
    )
    {
    }

    public static function make(
        string $model_name,
    ): self
    {
        return new self($model_name);
    }

    public function getAttributes(): array
    {
        return [
            'model' => $this->model_name,
        ];
    }
}
