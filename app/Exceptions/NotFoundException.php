<?php

namespace App\Exceptions;

use App\Http\Responses\Error;

class NotFoundException extends AbstractException
{
    public function __construct($message, $error)
    {
        $statusCode = 404;

        parent::__construct($statusCode, $error, $message);
    }
}
