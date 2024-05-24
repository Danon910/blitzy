<?php

declare(strict_types=1);

namespace Danon910\blitzy\Entities;

use Danon910\blitzy\Contracts\IResult;

class SuccessResult extends BaseResult implements IResult
{
    public function __construct(
        ?string $message = null,
        private readonly array $paths = [],
    )
    {
        parent::__construct($message);
    }

    public function isSuccess(): bool
    {
        return true;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }
}
