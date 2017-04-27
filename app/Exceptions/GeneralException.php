<?php

namespace App\Exceptions;

use App\Http\Responses\Error;

class GeneralException extends AbstractException
{
    public function __construct($message, $errorCode, $statusCode)
    {
        $error = new Error($errorCode);

        parent::__construct($statusCode, $error, $message);
    }
}
