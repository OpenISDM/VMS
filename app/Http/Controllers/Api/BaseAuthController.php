<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\AuthenticatedUserNotFoundException;
use App\Exceptions\JWTTokenNotFoundException;
use App\Services\JwtService;
use App\Traits\JwtAuthenticatable;

abstract class BaseAuthController extends Controller
{
    use JwtAuthenticatable;

    protected $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtInitialize();
        $this->jwtService = $jwtService;
    }
}
