<?php

declare(strict_types=1);

namespace WpIrbis\Exceptions;

use RuntimeException;

final class IrbisException extends RuntimeException
{
    private string $errorCodeName;

    public function __construct(string $message, string $errorCodeName = 'wp_irbis_error', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCodeName = $errorCodeName;
    }

    public function errorCodeName(): string
    {
        return $this->errorCodeName;
    }
}
