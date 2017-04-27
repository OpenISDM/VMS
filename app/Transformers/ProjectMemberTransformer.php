<?php

namespace App\Transformers;

use App\ProjectMember;
use League\Fractal\TransformerAbstract;

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
            'avatar_path',
        ];
        $userArray = $user->toArray();
        $item = array_only($userArray, $visibleFields);

        return $item;
    }
}
