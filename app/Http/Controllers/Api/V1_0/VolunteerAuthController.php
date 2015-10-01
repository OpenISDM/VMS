<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Requests\Api\V1_0\VolunteerRegistrationRequest;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

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
        
    }   
}