<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\VerifyEmailService;

class VerifyEmailServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected $volunteer;
    
    public function setUp() 
    {
        parent::setUp();
        $this->volunteer = $this->factoryVolunteer();
    }
    
    public function testEmailCompareSuccess() 
    {
        $service = new VerifyEmailService($this->volunteer, 'abc@abc.com', 'MY_VERIFICATION_CODE');

        $this->assertTrue($service->emailCompare());
    }

    /**
     * @expectedException App\Exceptions\AuthenticatedUserNotFoundException
     */
    public function testEmailCompareAuthenticatedUserNotFoundException()
    {
        $service = new VerifyEmailService($this->volunteer, 'wrong@abc.com', 'MY_VERIFICATION_CODE');
        $service->emailCompare();
    }

    public function testVerificationCodeCompareSuccess()
    {
        $this->volunteer->save();
        $verificationCode = factory(App\VerificationCode::class)->make(['code' => 'MY_VERIFICATION_CODE']);
        $verificationCode->volunteer()->associate($this->volunteer);
        $verificationCode->save();
        $service = new VerifyEmailService($this->volunteer, 'abc@abc.com', 'MY_VERIFICATION_CODE');

        $this->assertTrue($service->verificationCodeCompare());
    }

    /**
     * @expectedException App\Exceptions\NotFoundException
     */
    public function testVerificationCodeCompareEmptyNotFoundException()
    {
        $this->volunteer->save();
        $service = new VerifyEmailService($this->volunteer, 'abc@abc.com', 'MY_VERIFICATION_CODE');
        $service->verificationCodeCompare();
    }

    /**
     * @expectedException App\Exceptions\NotFoundException
     */
    public function testVerificationCodeCompareWrongCodeNotFoundException()
    {
        $this->volunteer->save();
        $verificationCode = factory(App\VerificationCode::class)->make(['code' => 'MY_VERIFICATION_CODE']);
        $verificationCode->volunteer()->associate($this->volunteer);
        $verificationCode->save();
        $service = new VerifyEmailService($this->volunteer, 'abc@abc.com', 'WRONG_VERIFICATION_CODE');

        $service->verificationCodeCompare();
    }

    public function testIsExpiredSuccess()
    {
        $this->volunteer->save();
        $verificationCode = factory(App\VerificationCode::class)->make(['code' => 'MY_VERIFICATION_CODE']);
        $verificationCode->volunteer()->associate($this->volunteer);
        $verificationCode->save();
        $service = new VerifyEmailService($this->volunteer, 'abc@abc.com', 'MY_VERIFICATION_CODE');

        $nowDateTime = new \DateTime();
        $this->assertFalse($service->isExpeired($nowDateTime));
    }

    /**
     * @expectedException App\Exceptions\NotFoundException
     */
    public function testIsExpiredException()
    {
        $this->volunteer->save();
        $verificationCode = factory(App\VerificationCode::class)->make(['code' => 'MY_VERIFICATION_CODE']);
        $verificationCode->volunteer()->associate($this->volunteer);
        $verificationCode->save();
        
        $service = new VerifyEmailService($this->volunteer, 'abc@abc.com', 'MY_VERIFICATION_CODE');
        $interval = new \DateInterval('PT9H');
        $nowDateTime = new \DateTime();
        $nowDateTime = $nowDateTime->add($interval);

        $service->isExpeired($nowDateTime);
    }
    
    protected function factoryVolunteer()
    {
        return factory(App\Volunteer::class)
            ->make([
                'username' => 'user01', 
                'password' => bcrypt('PASSW0RD01'), 
                'first_name' => 'Huang', 
                'last_name' => 'AMing', 
                'birth_year' => 1991, 
                'gender' => 'male', 
                'city_id' => 1, 
                'address' => 'MY Address', 
                'phone_number' => '0910123456', 
                'email' => 'abc@abc.com', 
                'emergency_contact' => 'Huang PAPA', 
                'emergency_phone' => '0988123456', 
                'avatar_path' => 'avatar.png', 
                'introduction' => 'Hi, my name is XXX'
        ]);
    }
}
