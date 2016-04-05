<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;

class CreateProjectRequest extends AbstractJsonRequest
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
        return [
            'data.type' => 'required|in:projects',
            'data.attributes' => 'required',
            'data.attributes.name' => 'required|string',
            'data.attributes.is_published' => 'required|boolean',
            'data.attributes.permission' => 'required|numeric'
        ];
    }
}
