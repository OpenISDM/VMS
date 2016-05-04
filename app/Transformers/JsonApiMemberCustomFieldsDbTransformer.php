<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\ProjectCustomField;
use App\MemberCustomFieldData;
use App\Transformers\JsonApiMemberTransformer;
use App\Transformers\JsonApiProjectCustomFieldTransformer;
use App\Volunteer;

class JsonApiMemberCustomFieldsDbTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'project_custom_field',
        'member'
    ];

    public function transform($value)
    {
        $payload = unserialize($value->data);

        return [
            'id' => $value->id,
            'data'=> $payload->get(),
            'created_at' => $value->created_at,
            'updated_at' => $value->updated_at
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
        $member = Volunteer::find($value->volunteer_id);

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
