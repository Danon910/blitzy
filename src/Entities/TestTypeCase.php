<?php

declare(strict_types=1);

namespace Danon910\blitzy\Entities;

class TestTypeCase
{
    public function __construct(
        private readonly string $case,
        private readonly string $expectation,
        private readonly array $before_given = [],
        private readonly array $given = [],
        private readonly array $when = [],
        private readonly array $then = [],
    )
    {
    }

    public function getCase(): string
    {
        return $this->case;
    }

    public function getExpectation(): string
    {
        return $this->expectation;
    }

    public function getBeforeGiven(): array
    {
        return $this->before_given;
    }

    public function getGiven(): array
    {
        return $this->given;
    }

    public function getWhen(): array
    {
        return $this->when;
    }

    public function getThen(): array
    {
        return $this->then;
    }
}
