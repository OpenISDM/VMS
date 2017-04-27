<?php

namespace App\Transformers\CustomField;

use App\ProjectCustomField;
use App\Services\TransformerService;
use League\Fractal\TransformerAbstract;

class UserCustomFieldWithDataTransformer extends TransformerAbstract
{
    public function transform(ProjectCustomField $value)
    {
        $result = $value->toArray();

        $customFieldData = $value->memberCustomFieldData->first();

        if (!empty($customFieldData)) {
            $manager = TransformerService::getDataArrayManager();
            $resource = TransformerService::getResourceItem(
                $customFieldData,
                'App\Transformers\CustomField\CustomFieldDataTransformer',
                'custom_field_data'
            );

            $customFieldDataResult = $manager->createData($resource)->toArray();
            unset($customFieldDataResult['data']['project_custom_field']);

            $result['custom_field_data'] = $customFieldDataResult['data'];
        } else {
            $result['custom_field_data'] = null;
        }

        return $result;
    }
}
