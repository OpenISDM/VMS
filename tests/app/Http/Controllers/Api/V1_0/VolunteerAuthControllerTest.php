<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Volunteer;
use App\VerificationCode;

class VolunteerAuthControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

    protected $postData;
    protected $apiKey;
    protected $exampleRoot;

    public function setUp()
    {
        parent::setUp();

        $this->exampleRoot = dirname(__FILE__) . '/../../../../../examples';
        $this->apiKey = '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1';
        $testExampleFilePath = $this->exampleRoot . '/register_post.json';
        $this->postData = json_decode(file_get_contents($testExampleFilePath), true);
    }

    public function testJsonRequestValidation()
    {
        $validationErrorPostData =
            json_decode(file_get_contents($this->exampleRoot . '/register_post_validation_error.json'), true);
        $expectedJsonResponseBody = [
            "errors" => [
                "password" => ["not_enough_password_strength"]
            ],
            "message" => "422 Unprocessable Entity",
            "status_code" => 422
        ];

        $this->factoryModel();
        $this->json('post', '/api/register', $validationErrorPostData, $this->unauthoirzedHeader)
             ->seeJson($expectedJsonResponseBody)
             ->assertResponseStatus(422);
    }

    public function testSuccessfulRegisteration()
    {
        $this->factoryModel();
        $this->expectsJobs(App\Jobs\SendVerificationEmail::class);

        StringUtil::shouldReceive('generateHashToken')
                        ->once()
                        ->andReturn('avatar123');

        $fileSystemMock = Mockery::mock('\Illuminate\Contracts\Filesystem\Filesystem');
        $fileSystemMock->shouldReceive('put')->once()->andReturn(true);
        Storage::shouldReceive('disk')
                      ->once()
                      ->with('avatar')
                      ->andReturn($fileSystemMock);

        $this->json('post', '/api/register', $this->postData, $this->unauthoirzedHeader)
             ->seeJson([
                'username' => $this->postData['username']
             ])
             ->assertResponseStatus(201);
        $this->seeInDatabase('volunteers', ['email' => $this->postData['email'], 'avatar_path' => 'avatar123.jpg']);
    }

    public function testSuccessfulEmailVerification()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $code = \App\Utils\StringUtil::generateHashToken();
        $verificationCode = factory(App\VerificationCode::class)->make([
            'code' => $code
        ]);
        $verificationCode->volunteer()->associate($volunteer);
        $verificationCode->save();

        $token = JWTAuth::fromUser($volunteer);

        $this->json('get',
                    '/api/email_verification/' . $volunteer->email . '/' . $code . '?token=' . $token,
                    [],
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals([
                'message' => 'Successful email verification'
             ])
             ->assertResponseStatus(200);

        $volunteerModel = Volunteer::find($volunteer->id);
        $codeModel = $volunteerModel->verificationCode;

        $this->assertEquals(1, $volunteerModel->is_actived);
        $this->assertNull($codeModel);
    }

    public function testSuccessfullyLogin()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $this->json('post',
                    '/api/auth',
                    [
                        'username' => $volunteer->username,
                        'password' => 'ThisIsMyPassW0Rd'
                    ],
                    $this->unauthoirzedHeader)
             ->seeJson([
                'href' => env('APP_URL') . '/api/users/me'
             ])
             ->assertResponseStatus(200);
    }

    public function testSuccessfullyLogout()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);
        $this->json('delete',
                    '/api/auth',
                    [],
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(204);
    }

    public function testResendEmailVerification()
    {
        $this->factoryModel();
        $this->expectsJobs(App\Jobs\SendVerificationEmail::class);

        $volunteer = factory(App\Volunteer::class)->create();
        $verificationCode = new VerificationCode(['code' => 'ABC123456']);
        $verificationCode->volunteer()->associate($volunteer);
        $verificationCode->save();

        $token = JWTAuth::fromUser($volunteer);
        $this->json('post',
                    '/api/resend_email_verification',
                    [],
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(204);

        $this->notSeeInDatabase('verification_codes', ['code' => 'ABC123456']);
    }
}
