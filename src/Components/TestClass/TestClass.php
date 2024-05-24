<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\TestClass;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Danon910\blitzy\Components\BaseComponent;

class TestClass extends BaseComponent
{
    public function __construct(
        private readonly string $namespace,
        private readonly string $name,
        private readonly array $imports,
        private readonly array $traits,
        private readonly array $methods,
    )
    {
    }

    public static function make(
        string $namespace,
        string $name,
        array $imports = [],
        array $traits = [],
        array $methods = [],
    ): self
    {
        return new self($namespace, $name, $imports, $traits, $methods);
    }

    public function getAttributes(): array
    {
        $traits = (new Collection($this->traits))
            ->map(function ($trait) {
                $trait_parts = explode('\\', $trait);

                if (is_array($trait_parts)) {
                    $prepared_trait = Collection::make($trait_parts)->last();

                    return Str::studly($prepared_trait);
                }

                return Str::studly($trait);
            })
            ->sortBy(fn(string $name) => strlen($name))
        ;

        $imports = (new Collection($this->imports))
            ->merge(['Tests\TestCase'])
            ->sortBy(fn(string $namespace) => strlen($namespace))
        ;

        return [
            'namespace' => Str::studly($this->namespace),
            'name' => Str::studly($this->name),
            'imports' => $imports,
            'traits' => $traits,
            'methods' => $this->methods,
        ];
    }
}
