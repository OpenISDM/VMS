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

    public function register(VolunteerRegistrationRequest $request)
    {
        //return response()->noContent();
        return 'qqq';
    }   
}