<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;

class CreateHyperlinkRequest extends AbstractJsonRequest
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
            'data.type' => 'required|in:hyperlinks',
            'data.attributes.name' => 'required',
            'data.attributes.link' => 'required|url',
            'data.relationships.project.data.type' => 'required|in:projects',
            'data.relationships.project.data.id' => 'required|exists:projects,id'
        ];
    }
}
