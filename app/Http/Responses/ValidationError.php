<?php

namespace App\Http\Responses;

class ValidationError implements \JsonSerializable
{
    protected $fields = [];
    protected $code = "";

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function addField($field)
    {
        array_push($this->fields, $field);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function jsonSerialize()
    {
        $errors = [];

        if (!empty($this-> fields)) {
            $errors['fields'] = $this->fields;
        }

        $errors['code'] = $this->code;

        return $errors;
    }
}