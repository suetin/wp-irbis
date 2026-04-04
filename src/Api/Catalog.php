<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use WpIrbis\Domain\CatalogResult;
use WpIrbis\Http\RequestResolver;
use WpIrbis\Rendering\TemplateRenderer;

final class Catalog
{
    public function __construct(
        private readonly SearchService $search,
        private readonly RequestResolver $requests,
        private readonly TemplateRenderer $templates
    ) {
    }

    public function render(array $args = []): string
    {
        $request = $this->requests->fromArray($args);
        $result = $this->search->search($request);

        return $this->templates->render(
            'catalog',
            [
                'request' => $request,
                'result' => $result,
            ]
        );
    }

    public function renderCurrentRequest(array $args = []): string
    {
        $request = $this->requests->fromGlobals($args);
        $result = $this->search->search($request);

        return $this->templates->render(
            'catalog',
            [
                'request' => $request,
                'result' => $result,
            ]
        );
    }

    public function search(array $args = []): array
    {
        return $this->searchResult($args)->toArray();
    }

    public function searchResult(array $args = []): CatalogResult
    {
        $request = $this->requests->fromArray($args);

        return $this->search->search($request);
    }
}
