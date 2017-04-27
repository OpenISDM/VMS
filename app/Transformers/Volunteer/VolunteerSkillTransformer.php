<?php

namespace App\Transformers\Volunteer;

use App\Skill;
use League\Fractal\TransformerAbstract;

class VolunteerSkillTransformer extends TransformerAbstract
{
    public function transform(Skill $skill)
    {
        $skillItem = [
            'name' => $skill->name,
        ];

        return $skillItem;
    }
}
