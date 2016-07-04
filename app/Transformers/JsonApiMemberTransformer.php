<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Hyperlink;
use App\Volunteer;
use App\Transformers\JsonApiProjectTransformer;

class JsonApiMemberTransformer extends TransformerAbstract
{
    public function transform(Volunteer $user)
    {
        $userArray = $user->toArray();
        $item = array_only($userArray, ['id', 'username', 'email', 'first_name', 'last_name']);
        $item['attending_at'] = $user->pivot->created_at;

        return $item;
    }
}
