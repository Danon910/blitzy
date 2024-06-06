<?php

declare(strict_types=1);

namespace Danon910\blitzy\Types;

use BackedEnum;
use Illuminate\Support\Str;
use Danon910\blitzy\Enums\TestAssertion;

abstract class BaseTestService
{
    protected function saveFile(string $namespace, string $filename, string $content): string
    {
        $namespace = str_replace('\\', '/', $namespace);

        if (!file_exists(base_path("tests/{$namespace}"))) {
            mkdir(base_path("tests/{$namespace}"), 0777, true);
        }

        $filename = Str::studly($filename);
        $content = '<?php' . PHP_EOL . PHP_EOL . $content;

        file_put_contents(base_path("tests/{$namespace}/{$filename}.php"), $content);

        return "tests/{$namespace}/{$filename}.php";
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
