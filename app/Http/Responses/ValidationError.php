<?php

namespace App\Http\Responses;

class ValidationError extends Error implements \JsonSerializable
{
    protected $fields = [];

    public function addField($field)
    {
        array_push($this->fields, $field);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function jsonSerialize()
    {
        $errors = [];

        if (!empty($this->fields)) {
            $errors['fields'] = $this->fields;
        }

        $errors['code'] = $this->code;

        return $errors;
    }
}
