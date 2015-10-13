<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Education;

class VolunteerEducationTransformer extends TransformerAbstract
{
    public function transform(Education $education)
    {
        $educationItem = [
            'education_id' => $education->volunteer->username . '_' . $education->id,
            'school' => $education->school,
            'degree' => (int) $education->degree,
            'start_year' => (int) $education->start_year,
            'end_year' => (int) $education->end_year
        ];

        if ($education->field_of_study) {
            $educationItem['field_of_study'] = $education->field_of_study;
        }

        return $educationItem;
    }
}
