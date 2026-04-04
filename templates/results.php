<?php

declare(strict_types=1);

$result = $context['result'] ?? null;
$items = $result instanceof \WpIrbis\Domain\CatalogResult ? $result->items : [];
$error = $result instanceof \WpIrbis\Domain\CatalogResult ? $result->error : null;
$hasQuery = $result instanceof \WpIrbis\Domain\CatalogResult ? $result->hasQuery : false;
$debug = $result instanceof \WpIrbis\Domain\CatalogResult ? $result->debug : [];
?>
<div class="wp-irbis-results">
    <?php if (! $hasQuery) : ?>
        <div class="wp-irbis-notice">
            <?php esc_html_e('Введите запрос для поиска по каталогу.', 'wp-irbis'); ?>
        </div>
    <?php elseif ($error instanceof \WP_Error) : ?>
        <div class="wp-irbis-notice wp-irbis-notice--error">
            <?php echo esc_html($error->get_error_message()); ?>
        </div>
    <?php elseif ($items === []) : ?>
        <div class="wp-irbis-notice">
            <?php esc_html_e('Ничего не найдено.', 'wp-irbis'); ?>
        </div>
    <?php else : ?>
        <div class="wp-irbis-results__list">
            <?php foreach ($items as $item) : ?>
                <?php echo irbis_catalog_template('book-card', ['book' => $item]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (! empty($debug['enabled'])) : ?>
        <div class="wp-irbis-notice" style="margin-top:16px;">
            <strong><?php esc_html_e('WP IRBIS Debug', 'wp-irbis'); ?></strong>
            <pre style="white-space:pre-wrap;margin:8px 0 0;"><?php echo esc_html(wp_json_encode([
                'environment' => $debug['environment'] ?? null,
                'connection_ok' => $debug['connection_ok'] ?? null,
                'search_by' => $debug['search_by'] ?? null,
                'search_string' => $debug['search_string'] ?? null,
                'search_category' => $debug['search_category'] ?? null,
                'expression' => $debug['expression'] ?? null,
                'limit' => $debug['limit'] ?? null,
                'found_count' => $debug['found_count'] ?? null,
                'rendered_count' => $debug['rendered_count'] ?? null,
                'base_url' => $debug['base_url'] ?? null,
                'error_code' => $debug['error_code'] ?? null,
                'error_message' => $debug['error_message'] ?? null,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
        </div>
    <?php endif; ?>
</div>
