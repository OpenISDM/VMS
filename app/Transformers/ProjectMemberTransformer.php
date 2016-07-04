<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Volunteer;

class ProjectMemberTransformer extends TransformerAbstract
{
    public function transform(Volunteer $user)
    {
        $visibleFields = [
            'id',
            'username',
            'first_name',
            'last_name',
            'email',
            'avatar_path'
        ];
        $userArray = $user->toArray();
        $item = array_only($userArray, $visibleFields);
        $item['attending_at'] = $user->pivot->created_at;

        return $item;
    }
}
