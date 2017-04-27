<?php

namespace App\Http\Requests\Api\V1_0;

use App\Http\Requests\AbstractJsonRequest;
use App\Project;
use Gate;

class InviteVolunteerInProjectRequest extends AbstractJsonRequest
{
    /**
     * Determine if the user is the project manager.
     *
     * @return bool
     */
    public function authorize()
    {
        // get the projectId from the route url
        $projectId = $this->route('projectId');
        // find if the projectId fits the projectId in the model
        $project = Project::findOrFail($projectId);

        // access control: check if the user is the project manager
        // if he/she is the pm, the gate will return true
        // Kind of like Gate -> AuthServiceProvider -> ProjectPolicy -> update
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
            'volunteers'      => 'required|array',
            'volunteers.*.id' => 'required|exists:volunteers,id',
        ];
    }
}
