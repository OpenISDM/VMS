<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Volunteer;
use App\VerificationCode;

class VolunteerAuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $postData;
    protected $apiKey;
    protected $headerArray = [];

    public function setUp()
    {
        parent::setUp();

        $this->apiKey = '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1';
        $this->headerArray = [
            'X-VMS-API-Key' => $this->apiKey
        ];

        $this->postData = json_decode(file_get_contents(__DIR__ . '/examples/register_post.json'), true);
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    //public function testRegister()
    //{
    //    $this->json('post', '/api/register', $this->postData, $this->headerArray)
    //         ->assertResponseStatus(200);
    //}

    public function testJsonRequestValidation()
    {
        $validationErrorPostData =
            json_decode(file_get_contents(__DIR__ . '/examples/register_post_validation_error.json'), true);
        $expectedJsonResponseBody = [
                            'message' => 'Validation failed',
                            'errors' => [[
                                    'fields' => ['password'],
                                    'code' => 'not_enough_password_strength'
                                ]]
                        ];

        $this->factoryModel();
        $this->json('post', '/api/register', $validationErrorPostData, $this->headerArray)
             ->seeJsonEquals($expectedJsonResponseBody)
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

        $this->json('post', '/api/register', $this->postData, $this->headerArray)
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
                    $this->headerArray)
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

    protected function factoryModel()
    {
        factory(App\ApiKey::class)->create([
            'api_key' => $this->apiKey
        ]);

        $countriesCitiesSeedData = [
            'Taiwan' => [
                'Taipei City',
                'New Taipei City',
                'Taoyuan City',
                'Taichung City',
                'Tainan City',
                'Hsinchu City',
                'Chiayi City',
                'Keelung City',
                'Hsinchu County',
                'Miaoli County',
                'Changhua County',
                'Nantou County',
                'Changhua County',
                'Yunlin County',
                'Chiayi County',
                'Pingtung County',
                'Yilan County',
                'Hualien County',
                'Taitung County',
                'Kinmen County',
                'Lienchiang County',
                'Penghu County',
            ]
        ];

        foreach ($countriesCitiesSeedData as $countryName => $cityList) {
            $country = factory(App\Country::class)
                        ->create(['name' => $countryName]);

            foreach ($cityList as $cityName) {
                $city = factory(App\City::class)->make(['name' => $cityName]);
                $city->country()->associate($country);
                $city->save();
            }
        }
    }
}
