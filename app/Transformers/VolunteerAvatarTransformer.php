<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Http\Responses\Avatar;

class VolunteerAvatarTransformer extends TransformerAbstract
{
    public function transform(Avatar $avatar)
    {
        $avatarItem = [
            'avatar_url' => $avatar->avatar_url,
            'avatar_name' => $avatar->avatar_name
        ];

        return $avatarItem;
    }
}