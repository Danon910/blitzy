<?php

declare(strict_types=1);

namespace Danon910\blitzy\Enums;

enum TestMarkAs: string
{
    case SKIPPED = 'skipped';
    case INCOMPLETE = 'incomplete';
    case RISKY = 'risky';

    public function message(string $message = ''): string
    {
        return match($this)
        {
            self::SKIPPED => '$this->markTestSkipped("'.$message.'");',
            self::INCOMPLETE => '$this->markTestIncomplete(' . $message . ');',
            self::RISKY => '$this->markAsRisky();',
        };
    }
}
