<?php

namespace App\Commands;

use App\Services\VerifyEmailService;

/**
 * Use \App\Services\VerifyEmailService to check the parameters and so on
 */
class VerifyEmailCommand implements CommandInterface
{
    protected $service;
    protected $nowDateTime;

    public function __construct(VerifyEmailService $service)
    {
        $this->service = $service;
        $this->nowDateTime = new \DateTime();
    }

    public function execute()
    {
        $this->service->emailCompare();
        $this->service->verificationCodeCompare();
        $this->service->isExpeired($this->nowDateTime);
        $this->service->activeVolunteer();
    }
}