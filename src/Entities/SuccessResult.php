<?php

declare(strict_types=1);

namespace Danon910\blitzy\Entities;

use Danon910\blitzy\Contracts\IResult;

class SuccessResult extends BaseResult implements IResult
{
    public function isSuccess(): bool
    {
        return true;
    }
}
