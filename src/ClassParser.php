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
        if (filled($only)) {
            return Collection::make($this->methods)
                ->filter(fn(ReflectionMethod $method) => $method->class === $this->path)
                ->filter(function (ReflectionMethod $method) use ($only) {
                    return in_array($method->name, $only);
                })
                ->toArray()
            ;
        }

        return $this->methods;
    }
}
