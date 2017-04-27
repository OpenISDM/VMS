<?php

namespace App\Transformers;

use App\MemberCustomFieldData;
use App\ProjectCustomField;
use League\Fractal\TransformerAbstract;

class ProjectMemberDataCustomFieldTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'projectCustomField' => 'custom_field',
        // 'member'
    ];

    public function transform(MemberCustomFieldData $value)
    {
        return $value->toArray();
    }

    public function includeCustomField(MemberCustomFieldData $value)
    {
        $projectCustomField = $value->projectCustomField()->first();

        if ($projectCustomField === null) {
            return $this->null();
        }

        $transformer = new ProjectCustomFieldTransformer();

        $result = $this->item(
            $projectCustomField,
            $transformer,
            'custom_field'
        );

        return $result;
    }

    // public function includeMember(MemberCustomFieldData $value)
    // {
    //     $member = $value->member()->first();
    //
    //     if ($member === null) {
    //         return $this->null();
    //     }
    //
    //     $transformer = new ProjectMemberTransformer();
    //
    //     $result = $this->item(
    //         $member,
    //         $transformer,
    //         'member'
    //     );
    //
    //     return $result;
    // }
}
