<?php

declare(strict_types=1);

namespace Danon910\blitzy;

use Danon910\blitzy\Enums\TestType;
use Danon910\blitzy\Entities\TestTypeCase;
use Danon910\blitzy\Entities\TestTypeConfig;

class BlitzyConfig
{
    public function getType(TestType $name): TestTypeConfig
    {
        $type_config = $this->getConfig(['types', $name->value]);

        $parsed_cases = [];

        foreach ((array)$type_config['cases'] as $case) {
            $parsed_cases[] = new TestTypeCase(
                $case['case'],
                $case['expectation'],
                $case['before_given'],
                $case['given'],
                $case['when'],
                $case['then'],
            );
        }

        return new TestTypeConfig(
            (array)$type_config['traits'] ?? [],
            (array)$type_config['only_methods'] ?? [],
            (bool)$type_config['generate_fsc'] ?? false,
                $parsed_cases,
        );
    }

    private function getConfig(array $keys, mixed $default = null): mixed
    {
        $keys_merged = implode('.', $keys);

        return config("blitzy.{$keys_merged}", $default);
    }
}
