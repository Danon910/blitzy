<?php

declare(strict_types=1);

namespace Danon910\blitzy\Providers;

use Illuminate\Support\ServiceProvider;
use Danon910\blitzy\Commands\GenerateTestCommand;

class BlitzyProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/blitzy.php' => config_path('blitzy.php')
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateTestCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/blitzy.php',
            'blitzy'
        );
    }
}
