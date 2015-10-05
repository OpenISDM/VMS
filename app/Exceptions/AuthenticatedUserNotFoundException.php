<?php

namespace App\Exceptions;

use App\Http\Responses\Error;

class AuthenticatedUserNotFoundException extends AbstractException
{
    public function __construct()
    {
        $message = 'Not Found';
        $error = new Error('volunteer_not_found');
        $statusCode = 404;

        parent::__construct($statusCode, $error, $message);
    }
}