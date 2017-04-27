<?php

namespace App\Repositories;

use App\Volunteer;
use DB;

class ProjectDbQueryRepository
{
    public function getViewableProjectsWithHyperlinks(Volunteer $user, $count = null)
    {
        $viewableProjectsQuery = $this->viewableProjectsQuery($user);
        $query = DB::table(DB::raw("({$viewableProjectsQuery->toSql()}) as sub"))
                    ->mergeBindings($viewableProjectsQuery)
                    ->leftJoin('hyperlinks', 'sub.id', '=', 'hyperlinks.project_id')
                    ->select('sub.*', 'hyperlinks.name AS hyperlink_name', 'hyperlinks.link')
                    ->orderBy('sub.updated_at');

        if (is_int($count)) {
            $query->paginate($count);
        }

        return $query->get();
    }

    public function getViewableProjects(Volunteer $user, $count = null)
    {
        $query = $this->viewableProjectsQuery($user);

        if (is_int($count)) {
            $query->paginate($count);
        }

        return $query->get();
    }

    protected function viewableProjectsQuery(Volunteer $user)
    {
        $manageProjects = $this->manageProjectsQuery($user);

        $asMemeberProjects = $this->asMemberProjectsQuery($user);

        $userLevelPermissionProjects = $this->userLevelPermissionProjectsQuery();

        $query = $manageProjects->union($asMemeberProjects)
                                ->union($userLevelPermissionProjects);

        return $query;
    }

    protected function manageProjectsQuery(Volunteer $user)
    {
        return DB::table('projects')
                ->leftJoin('project_manager_project', 'projects.id', '=', 'project_manager_project.project_id')
                ->leftJoin('project_volunteers', 'projects.id', '=', 'project_volunteers.project_id')
                ->where('project_manager_project.project_manager_id', '=', $user->id)
                ->select('projects.*');
    }

    protected function asMemberProjectsQuery(Volunteer $user)
    {
        return DB::table('projects')
                    ->leftJoin('project_manager_project', 'projects.id', '=', 'project_manager_project.project_id')
                    ->leftJoin('project_volunteers', 'projects.id', '=', 'project_volunteers.project_id')
                    ->where('project_volunteers.volunteer_id', '=', $user->id)
                    ->select('projects.*');
    }

    protected function userLevelPermissionProjectsQuery()
    {
        $query = DB::table('projects')

                    ->leftJoin('project_manager_project', 'projects.id', '=', 'project_manager_project.project_id')
                    ->leftJoin('project_volunteers', 'projects.id', '=', 'project_volunteers.project_id')
                    ->where('is_published', '=', true)
                    ->whereIn('projects.permission', [
                        config('constants.project_permission.PUBLIC'),
                        config('constants.project_permission.PRIVATE_FOR_USER'),
                    ])
                    ->select('projects.*');

        return $query;
    }
}
