<?php

namespace App\Services;

use JWTAuth;
use App\Exceptions\NotFoundException;
use App\Exceptions\AuthenticatedUserNotFoundException;

/**
 * The class is responsible for verifying email address.
 */
class VerifyEmailService
{
    // Authenticated volunteer
    protected $volunteer;

    // Inputed email address
    protected $realEmailAddress;

    // Inputed verification code
    protected $realVerificationCode;

    /**
     * Set inouted email and verification code
     * @param String $emailAddress     
     * @param String $verificationCode
     */
    public function __construct($volunteer, $emailAddress, $verificationCode)
    {
        $this->volunteer = $volunteer;
        $this->realEmailAddress = rawurldecode($emailAddress);
        $this->realVerificationCode = rawurldecode($verificationCode);
    }

    /**
     * Check if the verification code is expired.
     * If the verification token is expired, it will throw an exception
     * @param  \DateTime  $nowDateTime
     * @return boolean
     */
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

    /**
     * Compare the verification code.
     * If the verification code is not equal with real one, it
     * will throw an exception
     * @return boolean
     */
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

    /**
     * Compare the email address.
     * If the email is not correct , it will throw an exception
     * @return boolean [description]
     */
    public function emailCompare()
    {
        $real = $this->realEmailAddress;
        $expected = $this->volunteer->email;

        if (strcmp($expected, $real) !== 0) {
            throw new AuthenticatedUserNotFoundException();
        }

        return true;
    }

    /**
     * Activeate a volunteer and delete existing verification code
     */
    public function activeVolunteer()
    {
        $this->volunteer->verificationCode->delete();
        $this->volunteer->is_actived = true;
        $this->volunteer->save();
    }
}
