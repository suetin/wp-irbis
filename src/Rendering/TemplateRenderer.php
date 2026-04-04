<?php

declare(strict_types=1);

namespace WpIrbis\Rendering;

use WpIrbis\Contracts\RendererStrategy;

final class TemplateRenderer
{
    /**
     * @param RendererStrategy[] $strategies
     */
    public function __construct(private readonly array $strategies)
    {
    }

    public function render(string $template, array $context = []): string
    {
        $context = apply_filters('wp_irbis/template_context', $context, $template, '');

        foreach ($this->strategies as $strategy) {
            $markup = $strategy->render($template, $context);
            if ($markup !== '') {
                return $markup;
            }
        }

        return '';
    }
}
