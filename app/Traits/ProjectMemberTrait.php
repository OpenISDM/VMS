<?php

namespace App\Traits;

use App\Project;

/**
 *
 */
trait ProjectMemberTrait
{
    public function members()
    {
        return $this->belongsToMany('App\Volunteer',
            'project_volunteers',
            'project_id',
            'volunteer_id'
        )->withPivot(
            'status',
            'is_full_profile_permit',
            'permission'
        )->withTimestamps();
    }

    public function viewableMembers($user, Project $project)
    {
        $query = $this->members();
        $allowedPermissions = [
            config('constants.member_project_permission.PUBLIC')
        ];

        if ($user->inProject($project)) {
            $allowedPermissions[] = config('constants.member_project_permission.PRIVATE_FOR_MEMBER');
            $query = $query->where('volunteer_id', '=', $user->id)
                            ->orWhereIn('permission', $allowedPermissions);
        } else {
            $query = $query->whereIn('permission', $allowedPermissions);
        }

        return $query->get();
    }
}
