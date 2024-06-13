<?php

declare(strict_types=1);

namespace Danon910\blitzy\Enums;

enum TestHelper: string
{
    case TODO = 'todo';

    public function message(string $message = ''): string
    {
        return match($this)
        {
            self::TODO => empty($message) ? '// TODO' : '// TODO: ' . $message,
        };
    }
}
