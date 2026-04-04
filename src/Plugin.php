<?php

declare(strict_types=1);

namespace WpIrbis;

use WpIrbis\Admin\Settings;
use WpIrbis\Api\Catalog;
use WpIrbis\Api\ConnectionFactory;
use WpIrbis\Rendering\TemplateRenderer;
use WpIrbis\Shortcode\CatalogShortcode;
use WpIrbis\Support\Assets;

final class Plugin
{
    private static ?self $instance = null;

    private TemplateRenderer $templates;

    private Catalog $catalog;

    private function __construct()
    {
        $this->templates = new TemplateRenderer(WP_IRBIS_PATH . '/templates');
        $this->catalog = new Catalog(new ConnectionFactory(), $this->templates);
    }

    public static function boot(): void
    {
        if (self::$instance instanceof self) {
            return;
        }

        self::$instance = new self();
        self::$instance->register();
    }

    public static function instance(): self
    {
        if (! self::$instance instanceof self) {
            self::boot();
        }

        return self::$instance;
    }

    public function templates(): TemplateRenderer
    {
        return $this->templates;
    }

    public function catalog(): Catalog
    {
        return $this->catalog;
    }

    private function register(): void
    {
        (new Assets())->register();
        (new Settings())->register();
        (new CatalogShortcode($this->catalog))->register();
    }
}
