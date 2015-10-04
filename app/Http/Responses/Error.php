<?php

namespace App\Http\Responses;

class Error implements \JsonSerializable
{
    protected $code = "";

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function jsonSerialize()
    {
        $errors = ['code' => $this->code];
        
        return $errors;
    }
}
