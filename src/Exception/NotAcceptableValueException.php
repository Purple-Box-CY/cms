<?php

namespace App\Exception;

class NotAcceptableValueException extends \Exception
{
    public function __construct(string $message = 'Not acceptable value', int $code = 406, \Throwable $previous = null,)
    {
        parent::__construct($message, $code, $previous);
    }
}
