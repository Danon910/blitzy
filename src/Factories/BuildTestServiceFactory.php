<?php

declare(strict_types=1);

namespace Danon910\blitzy\Factories;

use Exception;
use Danon910\blitzy\Types\Smoke;
use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Contracts\ITestType;

class BuildTestServiceFactory
{
    public static function create(string $path, TestType $type, string $feature): ITestType
    {
        if ($type === TestType::SMOKE) {
            return new Smoke($path, $feature);
        }

        throw new Exception('Not found service.');
    }
}
