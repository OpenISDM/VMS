<?php

namespace App\Traits;

use App\Project;

trait ProjectMemberTrait
{
    public function members()
    {
        return $this->belongsToMany('App\Volunteer',
            'project_volunteers',
            'project_id',
            'volunteer_id'
        )->withPivot(
            'id',
            'status',
            'is_full_profile_permit',
            'permission'
        )->withTimestamps();
    }

    public function viewableMembers($user, Project $project)
    {
        $allowedPermissions = [
            config('constants.member_project_permission.PUBLIC'),
        ];

        if ($user->inProject($project)) {
            $allowedPermissions[] = config('constants.member_project_permission.PRIVATE_FOR_MEMBER');
            $query = $this->members()->where('volunteer_id', '=', $user->id)
                            ->orWhere('permission', '=', config('constants.member_project_permission.PUBLIC'))
                            ->orWhere('permission', '=', config('constants.member_project_permission.PRIVATE_FOR_MEMBER'));
        } else {
            $query = $this->members()->where('permission', '=', config('constants.member_project_permission.PUBLIC'));
        }

        return $query->get();
    }
}
