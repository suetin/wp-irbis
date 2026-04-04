<?php

declare(strict_types=1);

namespace WpIrbis\Api;

use Irbis\Connection;
use Irbis\MarcRecord;
use Irbis\SearchParameters;
use WpIrbis\Exceptions\IrbisException;

final class IrbisGateway
{
    public function __construct(private readonly ConnectionFactory $connections)
    {
    }

    /**
     * @return array<int, object>
     * @throws IrbisException
     */
    public function search(SearchParameters $parameters): array
    {
        try {
            $connection = $this->connections->make();
            $result = $connection->searchEx($parameters);
        } catch (\Throwable $exception) {
            throw $this->normalizeException($exception, 'wp_irbis_search_failed');
        }

        if ($result === false) {
            throw $this->lastErrorException($connection, 'wp_irbis_search_failed');
        }

        return is_array($result) ? $result : [];
    }

    /**
     * @param int[] $mfns
     * @return array<int, MarcRecord>
     * @throws IrbisException
     */
    public function readRecords(array $mfns): array
    {
        if ($mfns === []) {
            return [];
        }

        try {
            $connection = $this->connections->make();
            $records = $connection->readRecords($mfns);
        } catch (\Throwable $exception) {
            throw $this->normalizeException($exception, 'wp_irbis_read_records_failed');
        }

        if (! is_array($records) || ($records === [] && $connection->lastError < 0)) {
            throw $this->lastErrorException($connection, 'wp_irbis_read_records_failed');
        }

        $indexed = [];
        foreach ($records as $record) {
            if (! $record instanceof MarcRecord) {
                continue;
            }

            $indexed[(int) $record->mfn] = $record;
        }

        return $indexed;
    }

    private function lastErrorException(Connection $connection, string $errorCode): IrbisException
    {
        return new IrbisException(
            __('Ошибка при обмене с ИРБИС.', 'wp-irbis'),
            $errorCode,
            (int) $connection->lastError
        );
    }

    private function normalizeException(\Throwable $exception, string $errorCode): IrbisException
    {
        if ($exception instanceof IrbisException) {
            return $exception;
        }

        return new IrbisException(
            $exception->getMessage() !== '' ? $exception->getMessage() : __('Неизвестная ошибка ИРБИС.', 'wp-irbis'),
            $errorCode,
            (int) $exception->getCode(),
            $exception
        );
    }
}
