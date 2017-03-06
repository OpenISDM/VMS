<?php

namespace App\Transformers\Volunteer;

use League\Fractal\TransformerAbstract;
use App\Skill;

class VolunteerSkillTransformer extends TransformerAbstract
{
    public function transform(Skill $skill)
    {
        $skillItem = [
            'name' => $skill->name
        ];

        return $skillItem;
    }
}
