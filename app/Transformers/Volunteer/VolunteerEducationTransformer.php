<?php

namespace App\Transformers\Volunteer;

use League\Fractal\TransformerAbstract;
use App\Education;

class VolunteerEducationTransformer extends TransformerAbstract
{
    public function transform(Education $education)
    {
        $educationItem = [
            'id' => (int) $education->id,
            'school' => $education->school,
            'degree' => (int) $education->degree,
            'start_year' => (int) $education->start_year,
        ];

        if ($education->field_of_study) {
            $educationItem['field_of_study'] = $education->field_of_study;
        }

        if ($education->end_year) {
            $educationItem['end_year'] = (int) $education->end_year;
        }

        return $educationItem;
    }
}
