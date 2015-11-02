<?php

namespace App\Commands;

use App\Services\VerifyEmailService;

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
        $this->service->getAuthenticatedVolunteer();
        $this->service->emailCompare();
        $this->service->verificationCodeCompare();
        $this->service->isExpeired($this->nowDateTime);
    }
}