<?php

namespace App\Policies;

use App\Volunteer;
use App\Project;

class ProjectPolicy
{
    public function show(Volunteer $user, Project $project)
    {
        if ($user->isCreatorOfProject($project)) {
            return true;
        }

        if (!$project->is_published) {
            return false;
        }

        if ($project->permission == config('constants.project_permission.PUBLIC')) {
            return true;
        }

        if ($project->permission === config('constants.project_permission.PRIVATE_FOR_USER')) {
            return true;
        }

        if ($project->permission === config('constants.project_permission.PRIVATE_FOR_MEMBER')) {
            return $user->inProject($project);
        }

        return false;
    }

    public function update(Volunteer $user, Project $project)
    {
        return $user->isCreatorOfProject($project);
    }

    public function memberManipulate(Volunteer $user, Project $project)
    {
        return $user->isAttendingProject($project);
    }

    public function manage(Volunteer $user, Project $project)
    {
        return $user->isCreatorOfProject($project);
    }
}
