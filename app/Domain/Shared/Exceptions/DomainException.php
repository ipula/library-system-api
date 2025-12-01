<?php

namespace App\Domain\Shared\Exceptions;

abstract class DomainException extends \DomainException
{

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * HTTP status code to use when rendering this exception.
     */
    abstract public function getStatusCode(): int;
}
