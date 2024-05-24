<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components\TestMethod;

use Illuminate\Support\Str;
use Danon910\blitzy\Components\BaseComponent;

class TestMethod extends BaseComponent
{
    private array $annotations = [];

    public function __construct(
        private readonly string $method,
        private readonly string $case,
        private readonly string $expectation,
        private readonly array $before_given,
        private readonly array $given,
        private readonly array $when,
        private readonly array $then,
    )
    {
    }

    public static function make(
        string $method,
        string $case,
        string $expectation,
        array $before_given,
        array $given,
        array $when,
        array $then,
    ): self
    {
        return new self($method, $case, $expectation, $before_given, $given, $when, $then);
    }

    public function setAnnotations(array $annotations): self
    {
        $this->annotations = $annotations;

        return $this;
    }

    public function addAnnotations(array $annotation): void
    {
        $this->annotations = array_merge($this->annotations, $annotation);
    }

    public function getAttributes(): array
    {
        $name_parts = [
            Str::camel($this->method),
            Str::camel($this->case),
            Str::camel($this->expectation),
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
