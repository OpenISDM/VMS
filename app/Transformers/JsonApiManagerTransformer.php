<?php

namespace App\Transformers;

use App\Volunteer;
use League\Fractal\TransformerAbstract;

class JsonApiManagerTransformer extends TransformerAbstract
{
    public function transform(Volunteer $user)
    {
        $item = [
            'id' => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email
        ];

        return $item;
    }
}
