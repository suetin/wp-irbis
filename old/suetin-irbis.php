<?php
/*
Plugin Name: ИРБИС для WordPress
Version: 1.0.0
Author: Alexander Suetin
Author URI: https://suetin.ru
Text Domain: suetin_irbis
*/

/**
 * Plugin version
 */

use SuetinIrbis\View;
use SuetinIrbis\CatalogShortcode;

const SUETIN_IRBIS_PLUGIN_VERSION = '1.0.0';

/**
 * Root path plugin
 */
const SUETIN_IRBIS_PATH = __DIR__;

$settings = get_option('suetin_irbis_settings');

define("SUETIN_IRBIS_HOST", $settings['irbis_host']);
define("SUETIN_IRBIS_LOGIN", $settings['irbis_login']);
define("SUETIN_IRBIS_PASSWORD", $settings['irbis_password']);
define("SUETIN_IRBIS_DATABASE", $settings['irbis_database']);

/**
 * Path to assets
 */
define("SUETIN_IRBIS_PATH_TO_ASSETS", plugin_dir_url(__FILE__) . 'assets');

require SUETIN_IRBIS_PATH . '/vendor/autoload.php';

/**
 * Path to directory templates
 */
View::$view_dir = SUETIN_IRBIS_PATH . '/templates';

/**
 * Include assets
 */
add_action('wp_enqueue_scripts', 'suetin_irbis_scripts');
function suetin_irbis_scripts()
{
    wp_enqueue_style('style-suetin-irbis', SUETIN_IRBIS_PATH_TO_ASSETS . '/styles/style.css');
    wp_enqueue_script('script-suetin-irbis', SUETIN_IRBIS_PATH_TO_ASSETS . '/js/common.js', array('jquery'), SUETIN_IRBIS_PLUGIN_VERSION, true);
}

add_action('admin_init', function () {

    register_setting('general', 'suetin_irbis_settings');

    add_settings_section(
        'suetin_irbis_settings_section',
        __('Настройки подключения к ИРБИС серверу', 'suetin_irbis'),
        __return_empty_string(),
        'general'
    );

    add_settings_field(
        'irbis_host',
        __('IP сервера ИРБИС', 'suetin_irbis'),
        static function ($args) {
            $options = get_option('suetin_irbis_settings');
            $value = is_array($options) && isset($options['irbis_host']) ? $options['irbis_host'] : '';
            ?>
            <input id="<?php echo esc_attr($args['label_for']) ?>"
                   type="text"
                   name="suetin_irbis_settings[irbis_host]"
                   value="<?php echo esc_attr($value) ?>">
            <?php
        },
        'general',
        'suetin_irbis_settings_section',
        array('label_for' => 'suetin_irbis_settings_host-id')
    );

    add_settings_field(
        'irbis_login',
        __('Логин', 'suetin_irbis'),
        static function ($args) {
            $options = get_option('suetin_irbis_settings');
            $value = is_array($options) && isset($options['irbis_login']) ? $options['irbis_login'] : '';
            ?>
            <input id="<?php echo esc_attr($args['label_for']) ?>"
                   type="text"
                   name="suetin_irbis_settings[irbis_login]"
                   value="<?php echo esc_attr($value) ?>">
            <?php
        },
        'general',
        'suetin_irbis_settings_section',
        array('label_for' => 'suetin_irbis_settings_login-id')
    );

    add_settings_field(
        'irbis_password',
        __('Пароль', 'suetin_irbis'),
        static function ($args) {
            $options = get_option('suetin_irbis_settings');
            $value = is_array($options) && isset($options['irbis_password']) ? $options['irbis_password'] : '';
            ?>
            <input id="<?php echo esc_attr($args['label_for']) ?>"
                   type="password"
                   name="suetin_irbis_settings[irbis_password]"
                   value="<?php echo esc_attr($value) ?>">
            <?php
        },
        'general',
        'suetin_irbis_settings_section',
        array('label_for' => 'suetin_irbis_settings_password-id')
    );

    add_settings_field(
        'irbis_database',
        __('База данных', 'suetin_irbis'),
        static function ($args) {
            $options = get_option('suetin_irbis_settings');
            $value = is_array($options) && isset($options['irbis_database']) ? $options['irbis_database'] : '';
            ?>
            <input id="<?php echo esc_attr($args['label_for']) ?>"
                   type="text"
                   name="suetin_irbis_settings[irbis_database]"
                   value="<?php echo esc_attr($value) ?>">
            <?php
        },
        'general',
        'suetin_irbis_settings_section',
        array('label_for' => 'suetin_irbis_settings_database-id')
    );
});

/**
 * Шорткод для вывода формы поиска
 */
add_shortcode('irbis-catalog', static function () {
    return (new CatalogShortcode())->init();
});

if (! function_exists('suetin_irbis_notification')) {
    function suetin_irbis_notification($text, $status = 'error')
    {
        echo sprintf('<div class="suetin-irbis-notification suetin-irbis-notification--%1$s">%2$s</div>', $status, $text);
    }
}
