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

        $this->getVolunteerIdentifier();
    }

    /**
     * Check and get volunteer's identifier
     * @return null
     */
    protected function getVolunteerIdentifier()
    {
        try {
            if (! $this->volunteer = JWTAuth::parseToken()->authenticate()) {
                throw new AuthenticatedUserNotFoundException();
            }
        } catch (JWTException $e) {
            throw new JWTTokenNotFoundException($e);
        }
    }
}
