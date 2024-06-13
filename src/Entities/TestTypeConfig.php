<?php

declare(strict_types=1);

namespace Danon910\blitzy\Entities;

class TestTypeConfig
{
    public function __construct(
        private readonly array $traits = [],
        private readonly array $only_methods = [],
        private readonly bool $generate_fsc = false,
        private readonly array $cases = [],
    )
    {
    }

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function getOnlyMethods(): array
    {
        return $this->only_methods;
    }

    public function generateFsc(): bool
    {
        return $this->generate_fsc;
    }

    public function getCases(): array
    {
        return $this->cases;
    }
}
