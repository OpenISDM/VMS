<?php

namespace App\Transformers\CustomField;

use League\Fractal\TransformerAbstract;
use App\Volunteer;
use App\MemberCustomFieldData;
use App\Transformers\VolunteerProfileTransformer;
use App\Transformers\CustomField\CustomFieldDataTransformer;
use App\Services\TransformerService;

class MemberCustomFieldDataTransformer extends TransformerAbstract
{
    public function transform(Volunteer $user)
    {
        $manager = TransformerService::getDataArrayManager();

        $volunteerTransformer = new VolunteerProfileTransformer();
        $result = $volunteerTransformer->transform($user);


        $memberId = $user->pivot->id;

        $memberCustomFieldDataCollection = MemberCustomFieldData::where(
            'member_id', $memberId)->get();

        $memberCustomFieldDataResource = TransformerService::getResourceCollection(
                $memberCustomFieldDataCollection,
                'App\Transformers\CustomField\CustomFieldDataTransformer',
                'custom_field_data'
        );
        $customFieldDataResult = $manager->createData($memberCustomFieldDataResource)->toArray();

        // var_dump($customFieldDataResult);

        $result['custom_field_data'] = $customFieldDataResult['data'];

        return $result;
    }
}
