<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;

class FillCustomFieldRequest extends AbstractJsonRequest
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
            'data.type'                                 => 'required|in:project_custom_field_data',
            'data.attributes.content'                   => 'required',
            'data.relationships.custom_field.data.type' => 'required|in:custom_fields',
            'data.relationships.custom_field.data.id'   => 'required|exists:project_custom_field,id',
        ];
    }
}
