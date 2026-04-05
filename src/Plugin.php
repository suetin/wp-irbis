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
use WpIrbis\Rest\SearchController;
use WpIrbis\Support\UrlResolver;

final class Plugin
{
    private static ?self $instance = null;

    private Catalog $catalog;

    private SearchService $search;

    private RequestResolver $requests;

    private function __construct()
    {
        $this->requests = new RequestResolver(new UrlResolver());
        $this->search = new SearchService(
            new IrbisGateway(new ConnectionFactory()),
            new BookMapper()
        );

        $this->catalog = new Catalog($this->search, $this->requests);
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
        (new Settings())->register();
        (new SearchController($this->search, $this->requests))->register();
    }
}
