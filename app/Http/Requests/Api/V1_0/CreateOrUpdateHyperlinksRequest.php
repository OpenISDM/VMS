<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;
use App\Project;
use Gate;

class CreateOrUpdateHyperlinksRequest extends AbstractJsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $projectId = $this->route('projectId');

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
            'create.*.name'=> 'required',
            'create.*.link' => 'required|url',
            'update' => 'array',
            'update.*.id' => 'required|exists:hyperlinks,id',
            'update.*.name' => 'required',
            'update.*.link' => 'required|url'
        ];
    }
}
