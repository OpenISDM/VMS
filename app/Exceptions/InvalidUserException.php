<?php

namespace App\Exceptions;

use App\Http\Responses\Error;
use App\Exceptions\AbstractException;

class InvalidUserException extends AbstractException
{
    public function __construct($message, $errorCode)
    {
        $error = new Error('cannot_access');
        $statusCode = 400;

        parent::__construct($statusCode, $error, $message);
    }
}
