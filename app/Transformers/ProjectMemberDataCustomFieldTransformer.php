<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\ProjectCustomField;
use App\MemberCustomFieldData;
use App\Transformers\ProjectCustomFieldTransformer;

class ProjectMemberDataCustomFieldTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'projectCustomField' => 'custom_field'
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

        // var_dump($result);

        return $result;
    }
}
