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

$wp_irbis_vendor = WP_IRBIS_PATH . '/old/vendor/autoload.php';
if (file_exists($wp_irbis_vendor)) {
    require_once $wp_irbis_vendor;
}

$wp_irbis_iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(WP_IRBIS_PATH . '/src', FilesystemIterator::SKIP_DOTS)
);

foreach ($wp_irbis_iterator as $wp_irbis_file) {
    if ($wp_irbis_file->getExtension() !== 'php') {
        continue;
    }

    require_once $wp_irbis_file->getPathname();
}

\WpIrbis\Plugin::boot();

if (! function_exists('irbis_catalog')) {
    function irbis_catalog(array $args = []): string
    {
        return \WpIrbis\Plugin::instance()->catalog()->render($args);
    }
}

if (! function_exists('irbis_catalog_search')) {
    function irbis_catalog_search(array $args = []): array
    {
        return \WpIrbis\Plugin::instance()->catalog()->search($args);
    }
}

if (! function_exists('irbis_catalog_template')) {
    function irbis_catalog_template(string $template, array $context = []): string
    {
        return \WpIrbis\Plugin::instance()->templates()->render($template, $context);
    }
}
