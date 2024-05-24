<?php

declare(strict_types=1);

namespace Danon910\blitzy\Contracts;

interface IResult
{
    public function isSuccess(): bool;

    public function getMessage(): ?string;

    public function getPaths(): array;
}
