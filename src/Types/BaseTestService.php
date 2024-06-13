<?php

declare(strict_types=1);

namespace Danon910\blitzy\Types;

use BackedEnum;
use Illuminate\Support\Str;
use Danon910\blitzy\BlitzyConfig;
use Danon910\blitzy\Contracts\IResult;
use Danon910\blitzy\Enums\TestAssertion;
use Danon910\blitzy\Entities\FailResult;
use Danon910\blitzy\Entities\SuccessResult;

abstract class BaseTestService
{
    public function __construct(
        protected readonly BlitzyConfig $blitzy_config,
    )
    {
    }

    protected function saveFile(
        string $namespace,
        string $filename,
        string $content,
        bool $force,
    ): IResult
    {
        $namespace = str_replace('\\', '/', $namespace);
        $tests_folder_path = 'tests';

        if (!file_exists(base_path("{$tests_folder_path}/{$namespace}"))) {
            mkdir(base_path("{$tests_folder_path}/{$namespace}"), 0777, true);
        }

        $filename = Str::studly($filename);
        $content = '<?php' . PHP_EOL . PHP_EOL . $content;

        $test_filename = "{$tests_folder_path}/{$namespace}/{$filename}.php";

        if (!$force && file_exists($test_filename)) {
            return new FailResult('Test file already exist. If you want to overwrite test file use "--force" flag.', [$test_filename]);
        }

        file_put_contents(base_path($test_filename), $content);

        return new SuccessResult(paths: [$test_filename]);
    }

    protected function mapEnums(array $items): array
    {
        $mapped_items = [];

        foreach ($items as $item) {
            if ($item instanceof TestAssertion) {
                $mapped_items[] = $item->content();
                continue;
            }

            if ($item instanceof BackedEnum) {
                $mapped_items[] = $item->value;
                continue;
            }

            $mapped_items[] = $item;
        }

        return $mapped_items;
    }

}
