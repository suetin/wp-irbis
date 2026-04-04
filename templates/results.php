<?php

declare(strict_types=1);

$result = $context['result'] ?? null;
$items = $result instanceof \WpIrbis\Domain\CatalogResult ? $result->items : [];
$error = $result instanceof \WpIrbis\Domain\CatalogResult ? $result->error : null;
$hasQuery = $result instanceof \WpIrbis\Domain\CatalogResult ? $result->hasQuery : false;
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
</div>
