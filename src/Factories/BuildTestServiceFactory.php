<?php

declare(strict_types=1);

namespace Danon910\blitzy\Factories;

use Exception;
use Danon910\blitzy\Types\Smoke;
use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Types\Integration;
use Danon910\blitzy\Contracts\ITestType;
use Illuminate\Contracts\Foundation\Application;

class BuildTestServiceFactory
{
    public function __construct(
        protected readonly Application $app,
    )
    {
    }

    public function create(
        string $path,
        TestType $type,
        string $feature,
        array $methods,
        bool $force,
    ): ITestType
    {
        $test_type = match($type) {
            TestType::SMOKE => Smoke::class,
            TestType::INTEGRATION => Integration::class,
            TestType::UNIT => throw new \Exception('To be implemented'),
        };

        if ($test_type) {
            return $this->app->make($test_type, [
                'path' => $path,
                'feature' => $feature,
                'methods' => $methods,
                'force' => $force,
            ]);
        }

        throw new Exception('Not found service.');
    }
}
