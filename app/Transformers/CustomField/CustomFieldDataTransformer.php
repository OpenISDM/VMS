<?php

namespace App\Transformers\CustomField;

use League\Fractal\TransformerAbstract;
use App\MemberCustomFieldData;
use App\Transformers\ProjectCustomFieldTransformer;

class CustomFieldDataTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'project_custom_field'
    ];

    public function transform(MemberCustomFieldData $value)
    {
        return $value->toArray();
    }

    public function includeProjectCustomField(MemberCustomFieldData $customFieldData)
    {
        $customField = $customFieldData->projectCustomField()->first();

        $transformer = new ProjectCustomFieldTransformer();

        return $this->item($customField, $transformer);
    }
}
