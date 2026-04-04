<?php
/*
Plugin Name: WP IRBIS
Description: Интеграция WordPress с библиотечным каталогом ИРБИС.
Version: 0.1.0
Author: Alexander Suetin
Text Domain: wp-irbis
*/

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('WP_IRBIS_VERSION', '0.1.0');
define('WP_IRBIS_FILE', __FILE__);
define('WP_IRBIS_PATH', __DIR__);
define('WP_IRBIS_URL', plugin_dir_url(__FILE__));

$wp_irbis_vendor = WP_IRBIS_PATH . '/vendor/autoload.php';
if (! file_exists($wp_irbis_vendor)) {
    return;
}

require_once $wp_irbis_vendor;

\WpIrbis\Plugin::boot();

if (! function_exists('irbis_catalog')) {
    function irbis_catalog(array $args = []): string
    {
        return \WpIrbis\Plugin::instance()->catalog()->renderCurrentRequest($args);
    }
}

if (! function_exists('irbis_catalog_search')) {
    function irbis_catalog_search(array $args = []): array
    {
        return \WpIrbis\Plugin::instance()->catalog()->search($args);
    }
}

if (! function_exists('irbis_catalog_search_result')) {
    function irbis_catalog_search_result(array $args = []): \WpIrbis\Domain\CatalogResult
    {
        return \WpIrbis\Plugin::instance()->catalog()->searchResult($args);
    }
}

if (! function_exists('irbis_catalog_template')) {
    function irbis_catalog_template(string $template, array $context = []): string
    {
        return \WpIrbis\Plugin::instance()->templates()->render($template, $context);
    }
}
