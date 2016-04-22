<?php

namespace App\Repositories;

use App\MemberCustomFieldData;
use App\Project;
use App\Volunteer;
use App\ProjectMember;
use App\CustomField\Payload;

class MemberCustomFieldDataRepository
{
    public function updateOrCreate(Project $project, Volunteer $user, $customFieldId, $data)
    {
        $projectMemberRelationship = ProjectMember::where('volunteer_id', $user->id)
                                                ->where('project_id', $project->id)
                                                ->first();

        $customField = $project->customFields()
                               ->where('id', '=', $customFieldId)
                               ->firstOrFail();

        if ($this->existsByCustomFieldIdAndMemberId($customFieldId, $projectMemberRelationship->id)) {
            // Update
            $value = $customField->memberCustomFieldData()
                ->where('member_id', '=', $projectMemberRelationship->id)
                ->firstOrFail();
            $value->data = new Payload($data);
            $value->save();
        } else {
            // Save a new one
            $value = new MemberCustomFieldData([
                'data' => new Payload($data),
                'member_id' => $projectMemberRelationship->id
            ]);
            $customField->memberCustomFieldData()->save($value);
        }

        return $value;
    }

    public function updateOrCreateMany(Project $project, Volunteer $user, array $mapping)
    {
        $collection = collect($mapping);
        $values = [];

        $collection->each(function ($data, $customFieldId) use (&$value, $project, $user) {
            $value[] = $this->updateOrCreate($project, $user, $customFieldId, $data);
        });

        return $value;
    }

    public function getVolunteerAllCustomFieldData(Project $project, Volunteer $user)
    {
        $projectMemberRelationship = ProjectMember::where('volunteer_id', $user->id)
                                                ->where('project_id', $project->id)
                                                ->firstOrFail();
        /**
         * @TODO: Should use left join
         */
        $customFieldData = MemberCustomFieldData::where(
            'member_id',
            '=',
            $projectMemberRelationship->id
        )->get();

        return $customFieldData;
    }

    private function existsByCustomFieldIdAndMemberId($customFieldId, $memberId)
    {
        $count = MemberCustomFieldData::where('project_custom_field_id', '=', $customFieldId)
                                ->where('member_id', '=', $memberId)->count();

        return $count > 0;
    }
}
