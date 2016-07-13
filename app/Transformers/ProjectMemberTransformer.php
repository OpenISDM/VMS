<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\ProjectMember;

class ProjectMemberTransformer extends TransformerAbstract
{
    public function transform(ProjectMember $user)
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

        return $item;
    }
}
