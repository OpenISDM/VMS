<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Project;
use App\Transformers\CustomField\MemberCustomFieldDataTransformer;
use App\Transformers\ProjectCustomFieldTransformer;

class ProjectMemberWithCustomFieldDataTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'members',
        'customFields' => 'custom_fields'
    ];

    public function transform(Project $project)
    {
        // echo 'ProjectMemberWithCustomFieldDataTransformer';

        return $project->toArray();
    }

    public function includeMembers(Project $project)
    {
        $members = $project->members()->get();

        return $this->collection($members, new MemberCustomFieldDataTransformer);
    }

    public function includeCustomFields(Project $project)
    {
        $customFields = $project->customFields()->get();

        return $this->collection($customFields, new ProjectCustomFieldTransformer);
    }
}
