<?php

namespace App\Repositories;

use App\VerificationCode;
use App\Volunteer;

class VerificationCodeRepository
{
    public function create($data, Volunteer $volunteer)
    {
        $verificationCode = new VerificationCode($data);
        $verificationCode->volunteer()->associate($volunteer);
        $verificationCode->save();
    }
}
