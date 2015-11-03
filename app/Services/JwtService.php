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

    public function getVolunteer()
    {
        try {
            if (! $volunteer = JWTAuth::parseToken()->authenticate()) {
                throw new AuthenticatedUserNotFoundException();
            }
        } catch (JWTException $e) {
            throw new JWTTokenNotFoundException($e);
        }

        return $volunteer;
    }
}
