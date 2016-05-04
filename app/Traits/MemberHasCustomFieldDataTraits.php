<?php

namespace App\Traits;

/**
 *
 */
trait MemberHasCustomFieldDataTraits
{
    public function customFieldData()
    {
        return $this->hasManyThrough('App\MemberCustomFieldData',
            'App\ProjectMember',
            'volunteer_id',
            'member_id'
        );
    }

    public function getCustomFieldDataByProject(Project $project)
    {
        return $this->hasCustomFieldData()
                    ->projectCustomField()
                    ->where('project_id', '=', $project->id)
                    ->get();
    }
}
