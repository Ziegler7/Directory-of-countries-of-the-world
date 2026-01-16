<?php

namespace App\Model\Exceptions;

class InvalidCodeException extends \Exception {
    public function __construct(
        public readonly string $invalidCode,
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}