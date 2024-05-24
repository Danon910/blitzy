<?php

declare(strict_types=1);

namespace Danon910\blitzy\Entities;

abstract class BaseResult
{
    public function __construct(
        private readonly ?string $message = null,
    )
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    abstract public function isSuccess(): bool;
}
