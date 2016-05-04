<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;
use App\Project;
use Gate;

class CreateProjectCustomFieldRequest extends AbstractJsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $projectId = $this->route('id');
        $project = Project::findOrFail($projectId);

        return Gate::allows('update', $project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.type' => 'required|in:project_custom_field',
            'data.attributes.name' => 'required',
            'data.attributes.type' => 'required',
            'data.attributes.description' => 'required',
            'data.attributes.required' => 'required',
            'data.attributes.order' => 'required'
        ];
    }
}
