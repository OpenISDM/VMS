<?php

namespace App\Services;

use JWTAuth;
use App\Exceptions\UnauthorizedException;

class JwtService
{
    public function getToken($credentials) 
    {    
        // Authenticate
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new UnauthorizedException('Unauthorized', 'unauthorized');
        }

        return $token;
    }
}
