<?php

declare(strict_types=1);

$request = $context['request'] ?? null;
$searchBy = $request instanceof \WpIrbis\Domain\CatalogRequest ? $request->searchBy : 'title';
$baseUrl = $request instanceof \WpIrbis\Domain\CatalogRequest ? $request->baseUrl : '';
$searchString = $request instanceof \WpIrbis\Domain\CatalogRequest ? $request->searchString : '';
?>
<form method="get" class="wp-irbis-search-form" action="<?php echo esc_url($baseUrl); ?>" role="search">
    <div class="wp-irbis-search-form__modes">
        <label>
            <input type="radio" name="irbis_search_by" value="title" <?php checked($searchBy, 'title'); ?>>
            <span><?php esc_html_e('Название', 'wp-irbis'); ?></span>
        </label>
        <label>
            <input type="radio" name="irbis_search_by" value="author" <?php checked($searchBy, 'author'); ?>>
            <span><?php esc_html_e('Автор', 'wp-irbis'); ?></span>
        </label>
        <label>
            <input type="radio" name="irbis_search_by" value="keywords" <?php checked($searchBy, 'keywords'); ?>>
            <span><?php esc_html_e('Ключевые слова', 'wp-irbis'); ?></span>
        </label>
    </div>

    <div class="wp-irbis-search-form__row">
        <input
            type="text"
            name="irbis_search_string"
            value="<?php echo esc_attr($searchString); ?>"
            placeholder="<?php esc_attr_e('Введите поисковый запрос', 'wp-irbis'); ?>"
        >
        <button type="submit"><?php esc_html_e('Найти', 'wp-irbis'); ?></button>
    </div>
</form>
