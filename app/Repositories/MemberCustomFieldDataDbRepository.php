<?php

namespace App\Repositories;

use DB;
use App\Project;

class MemberCustomFieldDataDbRepository
{
    public function getAllProjectCustomFieldData(Project $project)
    {
        $query = DB::table('member_custom_field_data')
            ->join('project_custom_field',
                'member_custom_field_data.project_custom_field_id',
                '=',
                'project_custom_field.id')
            ->join('project_volunteers',
                'member_custom_field_data.member_id',
                '=',
                'project_volunteers.id')
            ->join('volunteers',
                'project_volunteers.volunteer_id',
                '=',
                'volunteers.id')
            ->where('project_custom_field.project_id', '=', $project->id)
            ->select('member_custom_field_data.*',
                'project_volunteers.*',
                'project_custom_field.*');

        return $query->get();
    }
}
