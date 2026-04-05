<?php

declare(strict_types=1);

namespace WpIrbis\Rest;

use WP_REST_Request;
use WP_REST_Response;
use WpIrbis\Api\SearchService;
use WpIrbis\Domain\CatalogResult;
use WpIrbis\Http\RequestResolver;

final class SearchController
{
    public function __construct(
        private readonly SearchService $search,
        private readonly RequestResolver $requests
    ) {
    }

    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        register_rest_route(
            'wp-irbis/v1',
            '/search',
            [
                'methods' => 'GET',
                'callback' => [$this, 'search'],
                'permission_callback' => '__return_true',
                'args' => [
                    'search_by' => ['type' => 'string'],
                    'search_string' => ['type' => 'string'],
                    'search_category' => ['type' => 'string'],
                    'filters' => ['type' => 'object'],
                    'limit' => ['type' => 'integer'],
                    'base_url' => ['type' => 'string'],
                ],
            ]
        );
    }

    public function search(WP_REST_Request $request): WP_REST_Response
    {
        $catalogRequest = $this->requests->searchFromArray($request->get_params());
        $result = $this->search->search($catalogRequest);

        return new WP_REST_Response(
            $this->prepareResponse($result),
            $result->error !== null ? 500 : 200
        );
    }

    private function prepareResponse(CatalogResult $result): array
    {
        $payload = $result->toArray();

        if ($result->error !== null) {
            $payload['error'] = [
                'code' => $result->error->get_error_code(),
                'message' => $result->error->get_error_message(),
            ];
        }

        return $payload;
    }
}
