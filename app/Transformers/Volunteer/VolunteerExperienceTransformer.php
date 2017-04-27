<?php

namespace App\Transformers\Volunteer;

use App\Experience;
use League\Fractal\TransformerAbstract;

class VolunteerExperienceTransformer extends TransformerAbstract
{
    public function transform(Experience $experience)
    {
        $experienceItem = [
            'id'         => (int) $experience->id,
            'company'    => $experience->company,
            'job_title'  => $experience->job_title,
            'start_year' => (int) $experience->start_year,
        ];

        if ($experience->end_year) {
            $experienceItem['end_year'] = (int) $experience->end_year;
        }

        return $experienceItem;
    }
}
