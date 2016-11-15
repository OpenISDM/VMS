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

    /**
     * To check if the $user is allow to see which projects the $viewer is attending
     * the $user is allow to see only the $user is the $viewer or,
     * the $user is a project manager of the $viewer
     * 
     * @param  Volunteer $viewer [description]
     * @param  Volunteer $user   [description]
     * @return [type]            [description]
     */
    public function showAttendingProjects(Volunteer $viewer, Volunteer $user)
    {
        if ($viewer->id === $user->id) {
            return true;
        }

        // $user->attendingProjects()->get() returns a collection(array) of the projects
        // the $user is attending
        $projects = $user->attendingProjects()->get();
        
        $count = $projects->load([
            'managers' => function ($query) use ($viewer) {
                $query->where('project_manager_id', $viewer->id);
            }])->count();

        return $count > 0;
    }
}
