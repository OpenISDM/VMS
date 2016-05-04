<?php

namespace App\Repositories;

use DB;
use App\Volunteer;

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

        return $query->orderBy('project_updated_at')->get();
    }

    protected function viewableProjectsQuery(Volunteer $user)
    {
        $manageProjects = $this->manageProjectsQuery($user)->select('projects.*', 'projects.updated_at as project_updated_at');
        $asMemeberProjects = $this->asMemberProjectsQuery($user)->select('projects.*', 'projects.updated_at as project_updated_at');
        $userLevelPermissionProjects = $this->userLevelPermissionProjectsQuery()->select('projects.*', 'projects.updated_at as project_updated_at');

        return $manageProjects->union($asMemeberProjects)
            ->union($userLevelPermissionProjects);
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
