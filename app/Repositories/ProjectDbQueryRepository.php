<?php

namespace App\Repositories;

use DB;
use App\Volunteer;

class ProjectDbQueryRepository
{
    public function getViewableProjectsByUser(Volunteer $user)
    {
        $manageProjects = $this->manageProjectsQuery($user)->select('projects.*');
        $asMemeberProjects = $this->asMemberProjectsQuery($user)->select('projects.*');
        $userLevelPermissionProjects = $this->userLevelPermissionProjectsQuery()->select('projects.*');

        return $manageProjects->union($asMemeberProjects)
            ->union($userLevelPermissionProjects)->get();
    }

    protected function manageProjectsQuery(Volunteer $user)
    {
        return DB::table('projects')
                ->join('project_manager_project', 'projects.id', '=', 'project_manager_project.project_id')
                ->where('project_manager_project.project_manager_id', '=', $user->id);
    }

    protected function asMemberProjectsQuery(Volunteer $user)
    {
        return DB::table('projects')
                    ->join('project_volunteers', 'projects.id', '=', 'project_volunteers.project_id')
                    ->where('project_volunteers.volunteer_id', '=', $user->id);
    }

    protected function userLevelPermissionProjectsQuery()
    {
        return DB::table('projects')
                    ->where('is_published', '=', true)
                    ->whereIn('permission', [
                        config('constants.project_permission.PUBLIC'),
                        config('constants.project_permission.PRIVATE_FOR_USER')
                    ]);
    }
}
