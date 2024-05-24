<?php

declare(strict_types=1);

namespace Danon910\blitzy\Components;

use Illuminate\Support\Facades\Blade;

abstract class BaseComponent
{
    abstract public function getAttributes(): array;

    public function getName(): string
    {
        return class_basename($this);
    }

    public function render(): string
    {
        $name = $this->getName();
        $template = file_get_contents(sprintf("%s/%s/%s.blade.php", __DIR__, $name, $name));

        $rendered = Blade::render($template, $this->getAttributes());

        $rendered = str_replace('#', '@', $rendered);
        $rendered = htmlspecialchars_decode($rendered);
        $rendered = rtrim($rendered);

        return $rendered;
    }
}
