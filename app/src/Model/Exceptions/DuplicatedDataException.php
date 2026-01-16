<?php

namespace App\Model\Exceptions;

class DuplicatedDataException extends \Exception {
    public function __construct(
        public readonly string $field,
        public readonly string $value,
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        $message = sprintf("Duplicate value '%s' for field '%s'", $value, $field);
        parent::__construct($message, $code, $previous);
    }
}