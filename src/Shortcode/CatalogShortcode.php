<?php

declare(strict_types=1);

namespace WpIrbis\Shortcode;

use WpIrbis\Api\Catalog;

final class CatalogShortcode
{
    private Catalog $catalog;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    public function register(): void
    {
        add_shortcode('irbis_catalog', [$this, 'render']);
        add_shortcode('irbis-catalog', [$this, 'render']);
    }

    public function render(array $atts = [], ?string $content = null, string $tag = ''): string
    {
        $atts = shortcode_atts(
            [
                'search_by' => null,
                'search_string' => null,
                'search_category' => null,
                'limit' => 10,
                'show_form' => true,
                'show_results' => true,
            ],
            $atts,
            $tag !== '' ? $tag : 'irbis_catalog'
        );

        return $this->catalog->renderCurrentRequest($atts);
    }
}
