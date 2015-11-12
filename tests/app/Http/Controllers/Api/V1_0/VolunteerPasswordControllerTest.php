<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VolunteerPasswordControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function testCreatePasswordResetSuccess()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();

        $headerArray = [
            'X-VMS-API-Key' => $this->getApiKey()
        ];

        // Mock mail
        Mail::shouldReceive('send')->once();

        $this->json('post',
            '/api/users/password_reset',
            ['email' => $volunteer->email],
            $headerArray
        )->assertResponseStatus(204);
    }

    public function testPostPasswordResetSuccess()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        
        $headerArray = [
            'X-VMS-API-Key' => $this->getApiKey()
        ];

        $credentials = [
            'email' => $volunteer->email,
            'token' => 'ABCToKeNStRiNg',
            'password' => 'VMSReSetPassw0Rd',
            'password_confirmation' => 'VMSReSetPassw0Rd'
        ];

        // Mock Password facades
        Password::shouldReceive('reset')
                        ->once()
                        ->with($credentials, Mockery::any())
                        ->andReturn(Password::PASSWORD_RESET);

        $this->json('put',
            '/api/users/password_reset/' . $volunteer->email . '/ABCToKeNStRiNg',
            ['password' => 'VMSReSetPassw0Rd'],
            $headerArray
        )
        ->assertResponseStatus(204);
    }

    public function testPostChangePassword()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $headerArray = [
            'Authorization' => 'Bearer ' . $token,
            'X-VMS-API-Key' => $this->getApiKey()
        ];

        $data = [
            'existing_password' => 'ThisIsMyPassW0Rd',
            'new_password' => 'MyNew1PASSWoRd'
        ];

        $this->json('put',
            '/api/users/me/password',
            $data,
            $headerArray
        )
        ->assertResponseStatus(204);
    }
}
