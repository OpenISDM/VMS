<?php

namespace App\Exceptions;

use App\Http\Responses\Error;

class InvalidUserException extends AbstractException
{
    public function __construct($message, $errorCode)
    {
        $error = new Error($errorCode);
        $statusCode = 400;

        parent::__construct($statusCode, $error, $message);
    }
}
