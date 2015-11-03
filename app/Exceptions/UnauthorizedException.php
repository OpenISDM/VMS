<?php

namespace App\Exceptions;

use App\Http\Responses\Error;
use App\Exceptions\AbstractException;

class UnauthorizedException extends AbstractException
{
    public function __construct($message, $error)
    {
        $statusCode = 401;

        parent::__construct($statusCode, $error, $message);
    }
}
