<?php

namespace App\Repositories;

use App\Volunteer;
use App\Repositories\CityRepository;
use App\Repositories\VerificationCodeRepository;

class VolunteerRepository
{
    /**
     * Create a new volunteer entity
     * @param  array $data
     * @return App\Volunteer
     */
    public function create(array $data) 
    {
        if (array_key_exists('city', $data)) {
            $city = $data['city'];

            unset($data['city']);
        }

        if (array_key_exists('password', $data)) {
            $data['password'] = bcrypt($data['password']);
        }

        $volunteer = Volunteer::firstOrNew($data);
        $volunteer->avatar_path = $data['avatar_path'];

        if (!empty($city)) {
            // Associate with city model
            $volunteer->city()->associate($city);
        }

        $volunteer->save();

        return $volunteer;
    }
}
