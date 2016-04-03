<?php

namespace App\Traits;

use App\Project;

/**
 *
 */
trait ManageProjectTraits
{
    public function manageProjects()
    {
        return $this->belongsToMany('App\Project',
            'project_manager_project',
            'project_manager_id',
            'project_id'
        );
    }

    public function isCreatorOfProject(Project $project)
    {
        return $this->manageProjects()->where('project_id', '=', $project->getKey())->first() ? true : false;
    }
}
