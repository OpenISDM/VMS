<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\Http\Controllers\Controller;
use App\Exceptions\AuthenticatedUserNotFoundException;
use App\Exceptions\JWTTokenNotFoundException;

abstract class BaseVolunteerController extends Controller
{
    protected $volunteer;
    
    public function __construct()
    {
        // For testing usage
        if (env('APP_ENV') == 'testing' && array_key_exists("HTTP_AUTHORIZATION", request()->server())) {
            JWTAuth::setRequest(\Route::getCurrentRequest());
        }
    }
}
