<?php

namespace App\Transformers\Volunteer;

use App\Http\Responses\Avatar;
use League\Fractal\TransformerAbstract;

class VolunteerAvatarTransformer extends TransformerAbstract
{
    public function transform(Avatar $avatar)
    {
        $avatarItem = [
            'avatar_url'  => $avatar->avatar_url,
            'avatar_name' => $avatar->avatar_name,
        ];

        return $avatarItem;
    }
}
