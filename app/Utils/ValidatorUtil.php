<?php

namespace App\Utils;

use App\Http\Responses\ValidationError;

class ValidatorUtil
{
    public static function formatter($errors)
    {
        $validationErrorArry = [];

        // Get messages in each field
        foreach ($errors as $field => $messages) {
            // Get each message in the same field
            foreach ($messages as $code) {
                if (!array_key_exists($code, $validationErrorArry)) {
                    $validationErro = new ValidationError($code);
                    $validationErrorArry[$code] = $validationErro;
                }

                $validationErrorArry[$code]->addField($field);
            }
        }

        $formatErrors = [];

        foreach ($validationErrorArry as $value) {
            array_push($formatErrors, $value);
        }

        return $formatErrors;
    }
}
