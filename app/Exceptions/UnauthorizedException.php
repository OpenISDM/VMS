<?php

namespace App\Exceptions;

class UnauthorizedException extends AbstractException
{
    public function __construct($message, $error)
    {
        $statusCode = 401;

        parent::__construct($statusCode, $error, $message);
    }
}
