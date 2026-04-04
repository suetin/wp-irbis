<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use Irbis\Connection;
use WpIrbis\Admin\Settings;
use function Irbis\describe_error;

final class ConnectionFactory
{
    public function make()
    {
        $settings = get_option(Settings::OPTION, []);
        $settings = is_array($settings) ? $settings : [];

        if (
            empty($settings['host']) ||
            empty($settings['login']) ||
            empty($settings['password']) ||
            empty($settings['database'])
        ) {
            return new \WP_Error(
                'wp_irbis_missing_settings',
                __('Плагин WP IRBIS не настроен.', 'wp-irbis')
            );
        }

        $connection = new Connection();
        $connection->host = (string) $settings['host'];
        $connection->username = (string) $settings['login'];
        $connection->password = (string) $settings['password'];
        $connection->database = (string) $settings['database'];

        if (! $connection->connect()) {
            return new \WP_Error(
                'wp_irbis_connection_failed',
                sprintf(
                    '%s %s',
                    __('Не удалось подключиться к ИРБИС.', 'wp-irbis'),
                    describe_error($connection->lastError)
                )
            );
        }

        return $connection;
    }
}
