<?php

namespace App\Exceptions;

use App\Http\Responses\Error;
use App\Exceptions\AbstractException;

class AccessDeniedException extends AbstractException
{
    public function __construct()
    {
        $message = 'Not have right to access';
        $error = new Error('cannot_access');
        $statusCode = 403;

        parent::__construct($statusCode, $error, $message);
    }
}
