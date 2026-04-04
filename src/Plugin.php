<?php

declare(strict_types=1);

namespace WpIrbis;

use WpIrbis\Admin\Settings;
use WpIrbis\Api\BookMapper;
use WpIrbis\Api\Catalog;
use WpIrbis\Api\ConnectionFactory;
use WpIrbis\Api\IrbisGateway;
use WpIrbis\Api\SearchService;
use WpIrbis\Http\RequestResolver;
use WpIrbis\Rendering\BladeRenderer;
use WpIrbis\Rendering\PhpRenderer;
use WpIrbis\Rendering\TemplateRenderer;
use WpIrbis\Rest\SearchController;
use WpIrbis\Shortcode\CatalogShortcode;
use WpIrbis\Support\Assets;
use WpIrbis\Support\UrlResolver;

final class Plugin
{
    private static ?self $instance = null;

    private TemplateRenderer $templates;

    private Catalog $catalog;

    private SearchService $search;

    private RequestResolver $requests;

    private Assets $assets;

    private function __construct()
    {
        $this->assets = new Assets();
        $this->templates = new TemplateRenderer([
            new BladeRenderer(),
            new PhpRenderer(WP_IRBIS_PATH . '/templates'),
        ]);

        $this->requests = new RequestResolver(new UrlResolver());
        $this->search = new SearchService(
            new IrbisGateway(new ConnectionFactory()),
            new BookMapper()
        );

        $this->catalog = new Catalog($this->search, $this->requests, $this->templates, $this->assets);
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

    public function search(): SearchService
    {
        return $this->search;
    }

    private function register(): void
    {
        $this->assets->register();
        (new Settings())->register();
        (new CatalogShortcode($this->catalog))->register();
        (new SearchController($this->search, $this->requests))->register();
    }
}
