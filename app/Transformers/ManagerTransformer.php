<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Volunteer;

class ManagerTransformer extends TransformerAbstract
{
    public function transform(Volunteer $volunteer)
    {
        $visibleFields = [
            'id',
            'username',
            'first_name',
            'last_name',
            'avatar_path',
            'email'
        ];

        $volunteerArray = $volunteer->toArray();
        $item = array_only($volunteerArray, $visibleFields);

        $item['avatar_url'] = config('vms.avatarHost') . '/' .
            config('vms.avatarRootPath') . '/' . $volunteerArray['avatar_path'];

        unset($item['avatar_path']);

        return $item;
    }
}
