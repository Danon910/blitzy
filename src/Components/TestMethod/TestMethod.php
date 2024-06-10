<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\TestMethod;

use Illuminate\Support\Str;
use Danon910\blitzy\Components\BaseComponent;

class TestMethod extends BaseComponent
{
    private array $before_given = [];
    private array $given = [];
    private array $when = [];
    private array $then = [];
    private array $annotations = [];

    public function __construct(
        private readonly string $method,
        private readonly string $case,
        private readonly string $expectation,
    )
    {
        $this->annotations = [
            'test' => null,
        ];
    }

    public static function make(
        string $method,
        string $case,
        string $expectation,
    ): self
    {
        return new self($method, $case, $expectation);
    }

    public function setBeforeGiven(array $before_given): void
    {
        $this->before_given = $before_given;
    }

    public function setGiven(array $given): void
    {
        $this->given = $given;
    }

    public function setWhen(array $when): void
    {
        $this->when = $when;
    }

    public function setThen(array $then): void
    {
        $this->then = $then;
    }

    public function addAnnotations(array $annotation): void
    {
        $this->annotations = array_merge($annotation, [null], $this->annotations);
    }

    public function getAttributes(): array
    {
        $name_parts = [
            Str::camel($this->method),
            Str::camel($this->case),
            Str::of($this->expectation)->lower()->camel(),
        ];

        return [
            'name' => implode('_', $name_parts),
            'before_given' => $this->before_given,
            'given' => $this->given,
            'when' => $this->when,
            'then' => $this->then,
            'annotations' => $this->annotations,
        ];
    }
}
