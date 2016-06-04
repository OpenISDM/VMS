<?php

namespace App\Traits;

use JWTAuth;

/**
 * Get JWT service triats
 */
trait JwtAuthenticatable
{
    protected function jwtInitialize()
    {
        // For testing usage
        // if (env('APP_ENV') == 'testing' && array_key_exists("HTTP_AUTHORIZATION", request()->server())) {
        //     JWTAuth::setRequest(\Route::getCurrentRequest());
        // }
    }
}
