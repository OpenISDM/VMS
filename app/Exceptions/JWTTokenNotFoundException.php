<?php

namespace App\Exceptions;

use App\Http\Responses\Error;

class JWTTokenNotFoundException extends AbstractException
{
    public function __construct(\Tymon\JWTAuth\Exceptions\JWTException $e)
    {
        $message = 'Server error';
        $error = new Error('cannot_get_token');
        $statusCode = $e->getStatusCode();

        parent::__construct($statusCode, $error, $message);
    }
}