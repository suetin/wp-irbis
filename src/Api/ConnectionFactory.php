<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use Irbis\Connection;
use WpIrbis\Admin\Settings;
use WpIrbis\Exceptions\IrbisException;
use function Irbis\describe_error;

final class ConnectionFactory
{
    /**
     * @throws IrbisException
     */
    public function make(): Connection
    {
        if (! function_exists('mb_internal_encoding')) {
            throw new IrbisException(
                __('Для работы WP IRBIS требуется расширение PHP mbstring.', 'wp-irbis'),
                'wp_irbis_missing_mbstring'
            );
        }

        $settings = get_option(Settings::OPTION, []);
        $settings = is_array($settings) ? $settings : [];

        if (
            empty($settings['host']) ||
            empty($settings['login']) ||
            empty($settings['password']) ||
            empty($settings['database'])
        ) {
            throw new IrbisException(
                __('Плагин WP IRBIS не настроен.', 'wp-irbis'),
                'wp_irbis_missing_settings'
            );
        }

        $connection = new Connection();
        $connection->host = (string) $settings['host'];
        $connection->username = (string) $settings['login'];
        $connection->password = (string) $settings['password'];
        $connection->database = (string) $settings['database'];

        if (! $connection->connect()) {
            throw new IrbisException(
                sprintf(
                    '%s %s',
                    __('Не удалось подключиться к ИРБИС.', 'wp-irbis'),
                    describe_error($connection->lastError)
                ),
                'wp_irbis_connection_failed',
                (int) $connection->lastError
            );
        }

        return $connection;
    }
}
