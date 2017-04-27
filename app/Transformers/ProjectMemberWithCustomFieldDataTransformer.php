<?php

namespace App\Transformers;

use App\Project;
use App\Transformers\CustomField\MemberCustomFieldDataTransformer;
use League\Fractal\TransformerAbstract;

class ProjectMemberWithCustomFieldDataTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'members',
        'customFields' => 'custom_fields',
    ];

    public function transform(Project $project)
    {
        // echo 'ProjectMemberWithCustomFieldDataTransformer';

        return $project->toArray();
    }

    public function includeMembers(Project $project)
    {
        $members = $project->members()->get();

        return $this->collection($members, new MemberCustomFieldDataTransformer());
    }

    public function includeCustomFields(Project $project)
    {
        $customFields = $project->customFields()->get();

        return $this->collection($customFields, new ProjectCustomFieldTransformer());
    }
}
