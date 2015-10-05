<?php

namespace App\Exceptions;

class ExceedingIndexException extends AbstractException
{
    public function __construct()
    {
        $message = 'Unable to execute';
        $error = new Error('exceeding_index_value');
        $statusCode = 400;

        parent::__construct($statusCode, $error, $message);
    }
}