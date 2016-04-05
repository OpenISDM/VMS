<?php

namespace App\Traits;

use App\Project;

/**
 *
 */
trait UserInProjectTraits
{
    public function projects()
    {
        return $this->belongsToMany('App\Project',
            'project_volunteers',
            'volunteer_id',
            'project_id'
        )->withPivot(
            'status',
            'is_full_profile_permit',
            'permission'
        )->withTimestamps();
    }

    public function pendingProjects()
    {
        return $this->projects()->where('status', config('constants.member_project_status.PENDING'));
    }

    public function attendingProjects()
    {
        return $this->projects()->where('status', config('constants.member_project_status.ATTENDING'));
    }

    public function isPendingProject(Project $project)
    {
        return $this->pendingProjects()->where($this->getKeyName(), '=', $project->getKey())->first() ? true : false;
    }

    public function inProject(Project $project)
    {
        return $this->projects()->where($this->getKeyName(), '=', $project->getKey())->first() ? true : false;
    }

    public function isAttendingProject(Project $project)
    {
        return $this->attendingProjects()->where($this->getKeyName(), '=', $project->getKey())->first() ? true : false;
    }

    public function attachProject(Project $project, $permission)
    {
        $this->projects()->attach($project->id, [
            'status' => config('constants.member_project_status.ATTENDING'),
            'is_full_profile_permit' => true,
            'permission' => $permission
        ]);
    }

    public function detachProject(Project $project)
    {
        $this->projects()->detach($project->id);
    }

    public function allowFullProfile(Project $project)
    {
        return $this->projects()
            ->updateExistingPivot(
                $project->id,
                ['is_full_profile_permit' => true],
                true
            );
    }
}
