<?php

declare(strict_types=1);

namespace WpIrbis\Contracts;

interface RendererStrategy
{
    public function render(string $template, array $context = []): string;
}
