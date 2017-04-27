<?php

namespace App\Policies;

use App\Education;
use App\Volunteer;

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
        // Make sure update action is able to be executed by owner
        return $volunteer->id === $education->volunteer_id;
    }

    public function delete(Volunteer $volunteer, Education $education)
    {
        // Make sure delete action is able to be executed by owner
        return $volunteer->id === $education->volunteer_id;
    }
}
