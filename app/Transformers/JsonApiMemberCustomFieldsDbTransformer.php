<?php

namespace App\Transformers;

use App\ProjectCustomField;
use League\Fractal\TransformerAbstract;


class JsonApiMemberCustomFieldsDbTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'project_custom_field',
        'member',
    ];

    public function transform($value)
    {
        $payload = unserialize($value->data);

        return [
            'id'         => $value->id,
            'data'       => $payload->get(),
            'created_at' => $value->created_at,
            'updated_at' => $value->updated_at,
        ];
    }

    public function includeProjectCustomField($value)
    {
        $projectCustomField = ProjectCustomField::find($value->project_custom_field_id);

        if ($projectCustomField === null) {
            return $this->null();
        }

        $transformer = new JsonApiProjectCustomFieldTransformer();
        $resourceKey = 'project_custom_fields';

        return $this->item(
            $projectCustomField,
            $transformer,
            $resourceKey
        );
    }

    public function includeMember($value)
    {
        $projectCustomField = ProjectCustomField::find($value->project_custom_field_id);
        $project = $projectCustomField->project()->first();
        $member = $project->members()->where('volunteer_id', $value->volunteer_id)->first();

        if ($member === null) {
            return $this->null();
        }

        $transformer = new JsonApiMemberTransformer();
        $resourceKey = 'members';

        return $this->item(
            $member,
            $transformer,
            $resourceKey
        );
    }
}
