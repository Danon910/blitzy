<?php

declare(strict_types=1);

namespace Danon910\blitzy\Enums;

enum TestType: string
{
    case SMOKE = 'smoke';
    case INTEGRATION = 'integration';
    case UNIT = 'unit';

    public function label(): string
    {
        return match($this)
        {
            self::SMOKE => 'Smoke',
            self::INTEGRATION => 'Integration',
            self::UNIT => 'Unit',
        };
    }
}
