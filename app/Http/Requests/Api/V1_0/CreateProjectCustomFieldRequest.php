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
        // @TODO
        // 'is_published' => 'boolean'

        return [
            'id' => 'exists:project_custom_field,id',
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
            'required' => 'required',
            'order' => 'required'
        ];
    }
}
