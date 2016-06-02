<?php

namespace App\Validators;

use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
use App\Utils\ArrayUtil;

class ArrayIndexValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        if (count($value) != 0) {
            $maxIndex = max($value);

            $data = $validator->getData();
            $other = Arr::get($data, $parameters[0]);

            if (ArrayUtil::isIndexExceed($other, $maxIndex)) {
                // Index exceeds $equipmentList size
                return false;
            }
        }

        return true;
    }

    public function message($message, $attribute)
    {
        return "exceeding_index_value";
    }
}
