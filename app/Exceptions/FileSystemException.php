<?php

namespace App\Exceptions;

use App\Http\Responses\Error;

class FileSystemException extends AbstractException
{
    public function __construct()
    {
        $message = 'Server error';
        $error = new Error('not_writable');
        $statusCode = 500;

        parent::__construct($statusCode, $error, $message);
    }
}
