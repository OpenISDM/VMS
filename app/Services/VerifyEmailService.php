<?php

namespace App\Services;

use JWTAuth;
use App\Exceptions\NotFoundException;

class VerifyEmailService
{
    protected $volunteer;

    protected $realEmailAddress;

    protected $realVerificationCode;

    public function __construct($emailAddress, $verificationCode)
    {
        $this->realEmailAddress = rawurldecode($emailAddress);
        $this->realVerificationCode = rawurldecode($verificationCode);
    }

    public function isExpeired($nowDateTime)
    {
        $createdTime = $this->volunteer->verificationCode->created_at;
        $expiredTime = config('vms.emailVerificationExpired', 8);   // hours
        $codeCreatedDateTime = new \DateTime($createdTime);
        $interval = new \DateInterval('PT' . $expiredTime . 'H');

        $expiredDateTime = $codeCreatedDateTime->add($interval);

        if ($nowDateTime > $expiredDateTime) {
            $message = 'Unvalidated or expired verification token';
            $error = 'unvalidated_expired_verification_token';
            
            throw new NotFoundException($message, $error);
        }

        return false;
    }

    public function verificationCodeCompare()
    {
        $real = $this->realVerificationCode;
        $verificationCode = $this->volunteer->verificationCode;

        if (empty($verificationCode)) {
            $message = 'Unvalidated or expired verification token';
            $error = 'unvalidated_expired_verification_token';
            
            throw new NotFoundException($message, $error);
        }

        $expected = $verificationCode->code;

        if (strcmp($expected, $real) !== 0) {
            $message = 'Unvalidated or expired verification token';
            $error = 'unvalidated_expired_verification_token';
            
            throw new NotFoundException($message, $error);
        }

        return true;
    }

    public function emailCompare()
    {
        $real = $this->realEmailAddress;
        $expected = $this->volunteer->email;

        if (strcmp($expected, $real) !== 0) {
            throw new AuthenticatedUserNotFoundException();
        }

        return true;
    }

    public function getAuthenticatedVolunteer()
    {
        // Get authenticated volunteer
        if (! $this->volunteer = JWTAuth::parseToken()->authenticate()) {
            throw new AuthenticatedUserNotFoundException();
        }

        return $this->volunteer;
    }
}