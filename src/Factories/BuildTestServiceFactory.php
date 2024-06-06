<?php

declare(strict_types=1);

namespace Danon910\blitzy\Factories;

use Exception;
use Danon910\blitzy\Types\Smoke;
use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Contracts\ITestType;
use Illuminate\Contracts\Foundation\Application;

class BuildTestServiceFactory
{
    public function __construct(
        protected readonly Application $app,
    )
    {
    }

    public function create(string $path, TestType $type, string $feature): ITestType
    {
        if ($type === TestType::SMOKE) {
            return $this->app->make(Smoke::class, [
                'path' => $path,
                'feature' => $feature,
            ]);
        }

        throw new Exception('Not found service.');
    }
}
