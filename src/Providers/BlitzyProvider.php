<?php

declare(strict_types=1);

namespace Danon910\blitzy\Providers;

use Illuminate\Support\ServiceProvider;
use Danon910\blitzy\Commands\GenerateTestCommand;

class BlitzyProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateTestCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/blitzie.php',
            'blitzie'
        );
    }
}
