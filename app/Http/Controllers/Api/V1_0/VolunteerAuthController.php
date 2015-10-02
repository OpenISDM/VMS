<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Requests\Api\V1_0\VolunteerRegistrationRequest;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use App\Vounteer;
use App\City;

class VolunteerAuthController extends Controller
{
    use Helpers;

    public function test()
    {
        return 'test';
    }

    /**
     * Register a new volunteer. The request will be validated by 
     * App\Http\Middleware\CheckHeaderFieldsMiddleware and 
     * App\Http\Requests\Api\V1_0\VolunteerRegistrationRequest classes
     * 
     * 
     * @param  VolunteerRegistrationRequest $request
     * @return [type]                                
     */
    public function register(VolunteerRegistrationRequest $request)
    {
        // Get volunteer data, except city object
        $volunteerInput = $request->except(['city']);
        // Get city ID
        //$cityId = $request->input('city.id');
        
        // Create a new volunteer
        //$volunteer = Volunteer::create($volunteerInput);
        //$city = City::find($cityId);
        //$volunteer->cities()->save($city);

        // Send verification email

        // Get authentication token
        
        return 'qqq';
    }   
}