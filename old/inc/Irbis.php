<?php

namespace SuetinIrbis;

use Irbis\Connection;
use function Irbis\describe_error;

class Irbis
{

    public function __construct()
    {
    }

    public function make_connection(): Connection
    {
        $connection = new Connection();
        $connection->host = SUETIN_IRBIS_HOST;
        $connection->username = SUETIN_IRBIS_LOGIN;
        $connection->password = SUETIN_IRBIS_PASSWORD;
        $connection->database = SUETIN_IRBIS_DATABASE;

        if (!$this->is_plugin_configured()) {
            wp_die('Плагин ИРБИС не настроен');
        }

        if (!$connection->connect()) {
            suetin_irbis_notification('Не удалось подключиться к БД ИРБИС! ' . describe_error($connection->lastError));
            exit();
        }

        return $connection;
    }

    /**
     * @return bool
     */
    private function is_plugin_configured(): bool
    {
        $options = get_option('suetin_irbis_settings');
        return is_array($options) && isset($options['irbis_login'], $options['irbis_password']) && ('' !== ($options['irbis_login']) && ('' !== ($options['irbis_password'])));
    }
}
