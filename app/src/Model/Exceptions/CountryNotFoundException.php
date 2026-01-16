<?php

namespace App\Model\Exceptions;

class CountryNotFoundException extends \Exception {
    public function __construct(
        public readonly string $notFoundCode,
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}