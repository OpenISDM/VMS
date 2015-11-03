<?php

namespace App\Repositories;

use App\City;

class CityRepository
{
    public function findById($id)
    {
        return City::find($id);
    }
}
