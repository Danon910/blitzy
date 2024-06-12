<?php

declare(strict_types=1);

namespace Danon910\blitzy;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Collection;

class ClassParser
{
    protected ?string $path = null;
    protected ?ReflectionClass $reflection_class = null;
    protected array $methods = [];

    public function parse(string $path): self
    {
        $this->path = $path;
        $this->reflection_class = new ReflectionClass($path);
        $this->methods = $this->reflection_class->getMethods();

        return $this;
    }

    public function getPath(): string
    {
        return $this->reflection_class->getName();
    }


    public function getName(): string
    {
        return $this->reflection_class->getShortName();
    }

    public function getMethods(array $only = []): array
    {
        return Collection::make($this->methods)
            ->filter(function (ReflectionMethod $method) {
                return $method->class === $this->path;
            })
            ->when(filled($only), function (Collection $collection) use ($only) {
                return $collection->filter(fn(ReflectionMethod $method) => in_array($method->name, $only));
            })
            ->toArray()
        ;
    }
}
