<?php

declare(strict_types=1);

namespace Danon910\blitzy\Commands;

use Throwable;
use Illuminate\Console\Command;
use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Factories\BuildTestServiceFactory;

class GenerateTestCommand extends Command
{
    const SUCCESS = 0;
    const FAILURE = 1;

    protected $signature = 'blitzy:generate {path} {--type=} {--feature=TODO} {--methods=} {--force}';
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
                    TestType::INTEGRATION->value => TestType::INTEGRATION->label(),
                ],
                TestType::SMOKE->value
            );
        }

        if (!in_array($type, TestType::available())) {
            $this->error(sprintf("Wrong test type. Available types: %s", implode(', ', TestType::available())));

            return self::FAILURE;
        }

        $type = TestType::from(mb_strtolower($type));

        $path = $this->argument('path');
        $feature = $this->option('feature');
        $methods = $this->getValues($this->option('methods'));
        $force = $this->option('force');

        $start_time = microtime(true);
        $file_path = sprintf("%s.php", str_replace('\\', '/', $path));

        if (count(array_filter([
            file_exists($file_path),
            file_exists(lcfirst($file_path)),
        ])) === 0) {
            $this->error("File does not exist: {$file_path}");

            return self::FAILURE;
        }

        try {
            /** @var BuildTestServiceFactory $factory */
            $factory = app()->make(BuildTestServiceFactory::class);
            $test_service = $factory->create($path, $type, $feature, $methods, $force);
            $generated_test_result = $test_service->build();

            if ($generated_test_result->isSuccess()) {
                $end_time = microtime(true);
                $time = round($end_time - $start_time, 2);

                if (count($generated_test_result->getPaths()) === 0) {
                    $this->warn('No tests generated');

                    return self::SUCCESS;
                }

                $this->info(sprintf('%s [%ss]', $generated_test_result->getMessage(), $time));

                foreach ($generated_test_result->getPaths() as $index => $generated_test_path) {
                    $index++;
                    $this->line("({$index}) {$generated_test_path}");
                }

                return self::SUCCESS;
            }

            $this->warn($generated_test_result->getMessage());

            if (count($generated_test_result->getPaths()) > 0) {
                foreach ($generated_test_result->getPaths() as $test_path) {
                    $this->warn($test_path);
                }
            }
        } catch (Throwable $exception) {
            $this->error('Fatal Error occurred!');
            $this->error($exception->getMessage());
            $this->error($exception->getTraceAsString());
        }

        return self::FAILURE;
    }

    private function getValues(string|array|bool|null $value): array
    {
        if (is_null($value)) {
            return [];
        }

        return explode(',', $value);
    }
}
