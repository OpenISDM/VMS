<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        //->seeJsonEquals(['a' => 'a'])
        ->assertResponseStatus(204);
    }
}
