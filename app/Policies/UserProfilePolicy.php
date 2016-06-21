<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Volunteer;

class UserProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function showAttendingProjects(Volunteer $viewer, Volunteer $user)
    {
        if ($viewer->id === $user->id) {
            return true;
        }

        $projects = $user->attendingProjects()->get();
        $count = $projects->load([
            'managers' => function ($query) use ($viewer) {
                $query->where('project_manager_id', $viewer->id);
            }])->count();

        return $count > 0;
    }
}
