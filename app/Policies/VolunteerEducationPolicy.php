<?php

namespace App\Policies;

use App\Volunteer;
use App\Education;

class VolunteerEducationPolicy
{
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(Volunteer $volunteer, Education $education)
    {
        return $volunteer->id === $education->volunteer_id;
    }

    public function delete(Volunteer $volunteer, Education $education)
    {
        return $volunteer->id === $education->volunteer_id;
    }
}
