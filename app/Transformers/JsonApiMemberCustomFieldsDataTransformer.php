<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\ProjectCustomField;
use App\MemberCustomFieldData;
use App\Transformers\JsonApiProjectCustomFieldTransformer;

class JsonApiMemberCustomFieldsDataTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'project_custom_field'
    ];

    public function transform(MemberCustomFieldData $value)
    {
        return $value->toArray();
    }

    public function includeProjectCustomField(MemberCustomFieldData $value)
    {
        $projectCustomField = $value->projectCustomField()->first();

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
}
