<?php

namespace App\Exceptions;

use App\Http\Responses\Error;
use App\Exceptions\AbstractException;

class TypeErrorException extends AbstractException
{
    public function __construct()
    {
        $message = 'Something wrong';
        $error = new Error('incorrect_type');
        $statusCode = 400;

        parent::__construct($statusCode, $error, $message);
    }
}
