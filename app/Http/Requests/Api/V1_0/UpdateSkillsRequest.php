<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;

class UpdateSkillsRequest extends AbstractJsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // TODO: array validation
        return [
            'skills' => 'required',
            'existing_skill_indexes' => 'sometimes|array_index:skills',
        ];
    }
}
