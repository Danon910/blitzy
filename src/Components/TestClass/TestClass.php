<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\TestClass;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Danon910\blitzy\Components\BaseComponent;

class TestClass extends BaseComponent
{
    private array $imports = [];
    private array $traits = [];
    private array $methods = [];

    public function __construct(
        private readonly string $namespace,
        private readonly string $name,
    )
    {
    }

    public static function make(
        string $namespace,
        string $name,
    ): self
    {
        return new self($namespace, $name);
    }

    public function setImports(array $imports): void
    {
        $this->imports = $imports;
    }

    public function setTraits(array $traits): void
    {
        $this->traits = $traits;
    }

    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
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
