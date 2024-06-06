<?php

declare(strict_types=1);

namespace Danon910\blitzy\Commands;

use Throwable;
use Illuminate\Console\Command;
use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Factories\BuildTestServiceFactory;

class GenerateTestCommand extends Command
{
    protected $signature = 'blitzy:generate {path} {--type=} {--feature=TODO}';
    protected $description = 'Generate smoke test template for specific path.';

    public function handle(): int
    {
        if ($this->option('type')) {
            $type = $this->option('type');
        } else {
            $type = $this->choice(
                'What type of test you want to generate?',
                [
                    TestType::SMOKE->value => TestType::SMOKE->label(),
                ],
                TestType::SMOKE->value
            );
        }

        $type = TestType::from(mb_strtolower($type));

        $path = $this->argument('path');
        $feature = $this->option('feature');

        $start_time = microtime(true);

        try {
            /** @var BuildTestServiceFactory $factory */
            $factory = app()->make(BuildTestServiceFactory::class);
            $test_service = $factory->create($path, $type, $feature);
            $generated_test_result = $test_service->build();

            if ($generated_test_result->isSuccess()) {
                $end_time = microtime(true);
                $time = round($end_time - $start_time, 2);

                $this->info(
                    sprintf('%s [%ss]', $generated_test_result->getMessage(), $time)
                );

                foreach ($generated_test_result->getPaths() as $index => $generated_test_path) {
                    $index++;
                    $this->line("({$index}) {$generated_test_path}");
                }

                return 0;
            }

            $this->error('Error occurred!');
            $this->error($generated_test_result->getMessage());
        } catch (Throwable $exception) {
            $this->error('Fatal Error occurred!');
            $this->error($exception->getMessage());
            $this->error($exception->getTraceAsString());
        }

        return 1;
    }
}
