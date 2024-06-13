<?php

declare(strict_types=1);

namespace Danon910\blitzy\Entities;

abstract class BaseResult
{
    public function __construct(
        private readonly ?string $message = null,
        private readonly array $paths = [],
    )
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    abstract public function isSuccess(): bool;
}
