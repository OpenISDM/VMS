<?php

namespace App\Policies;

use App\Volunteer;
use App\Experience;

class VolunteerExperiencePolicy
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

    public function update(Volunteer $volunteer, Experience $experience)
    {
        return $volunteer->id === $experience->volunteer_id;
    }

    public function delete(Volunteer $volunteer, Experience $experience)
    {
        return $volunteer->id === $experience->volunteer_id;
    }
}
