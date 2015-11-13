<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\ExceedingIndexException;

class VolunteerProfileControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

    protected $apiKey;
    protected $exampleRoot;

    public function setUp()
    {
        parent::setUp();

        $this->exampleRoot = dirname(__FILE__) . '/../../../../../examples';
    }

    public function testSuccessfullyUpdateSkillsMe()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $postData = [
            'skills' => [
                'Swimming',
                'Programming',
                'Repo rescue'
            ],
            'existing_skill_indexes' => [
            ]
        ];

        $this->json('post',
                    '/api/users/me/skills',
                    $postData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ])
             ->assertResponseStatus(204);

        $testVolunter = App\Volunteer::find($volunteer->id);

        foreach ($postData['skills'] as $skill) {
            $testSkill = $testVolunter->skills()->where('name', $skill)->first();
            $this->assertEquals($skill, $testSkill->name);
        }
    }

    public function testUpdateSkillsMeExceedingIndexException()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $postData = [
            'skills' => [
                'Swimming',
                'Programming',
                'Repo rescue'
            ],
            'existing_skill_indexes' => [
                1,
                2,
                3,
                4
            ]
        ];

        foreach ($postData['skills'] as $skill) {
            $volunteer->skills()->create(['name' => $skill]);
        }

        $this->json('post',
                    '/api/users/me/skills',
                    $postData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ])
             ->seeJsonEquals([
                        'message' => 'Unable to execute',
                        'errors' => [[
                            'code' => 'exceeding_index_value'
                        ]]
                    ])
             ->assertResponseStatus(400);
    }

    public function testShowMe()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;
        $volunteer->save();

        $skills = ['Swimming', 'Programming'];
        $equipment = ['Car', 'Scooter', 'Camera'];

        foreach ($skills as $skill) {
            $volunteer->skills()
                 ->firstOrCreate(['name' => $skill]);
        }

        foreach ($equipment as $eq) {
            $volunteer->equipment()
                 ->firstOrCreate(['name' => $eq]);
        }

        $token = JWTAuth::fromUser($volunteer);

        $this->json('get',
                    '/api/users/me', [], [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ])
             ->seeJson([
                        'username' => $volunteer->username,
                        'first_name' => $volunteer->first_name,
                        'last_name' => $volunteer->last_name,
                        'birth_year' => $volunteer->birth_year,
                        'gender' => $volunteer->gender,
                        'city' => ['id' => 1, 'name_en' => 'Taipei City'],
                        'address' => $volunteer->address,
                        'phone_number' => $volunteer->phone_number,
                        'email' => $volunteer->email,
                        'emergency_contact' => $volunteer->emergency_contact,
                        'emergency_phone' => $volunteer->emergency_phone,
                        'introduction' => 'Hi, my name is XXX',
                        'experiences' => ['href' => env('APP_URL') . '/api/users/me/experiences'],
                        'educations' => ['href' => env('APP_URL') . '/api/users/me/educations'],
                        'skills' => [
                            [
                                'name' => 'Swimming',
                                'id' => 1,
                            ],
                            [
                                'name' => 'Programming',
                                'id' => 2,
                            ]
                        ],
                        'equipment' => [
                            [
                                'name' => 'Car',
                                'id' => 1,
                            ],
                            [
                                'name' => 'Scooter',
                                'id' => 2,
                            ],
                            [
                                'name' => 'Camera',
                                'id' => 3,
                            ]
                        ],
                        'projects' => [
                            'href' => env('APP_URL') . '/api/users/me/projects'
                        ],
                        'processes' => [
                            'participating_number' => 0,
                            'participated_number' => 0,
                            'href' => env('APP_URL') . '/api/users/me/processes'
                        ],
                        'avatar_url' => config('vms.avatarHost') . '/upload/avatars/' . $volunteer->avatar_path,
                        'is_actived' => $volunteer->is_actived
                    ])
             ->assertResponseStatus(200);
    }

    public function testUpdateMe()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;
        $volunteer->save();

        $skills = ['Swimming', 'Programming'];
        $equipment = ['Car', 'Scooter', 'Camera'];

        foreach ($skills as $skill) {
            $volunteer->skills()
                 ->firstOrCreate(['name' => $skill]);
        }

        foreach ($equipment as $eq) {
            $volunteer->equipment()
                 ->firstOrCreate(['name' => $eq]);
        }

        $token = JWTAuth::fromUser($volunteer);
        $putData = [
            'city' => ['id' => 2],
            'emergency_phone' => '0910123456'
        ];

        $this->json('put',
                    '/api/users/me', $putData, [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ])
             ->seeJson([
                        'username' => $volunteer->username,
                        'first_name' => $volunteer->first_name,
                        'last_name' => $volunteer->last_name,
                        'birth_year' => $volunteer->birth_year,
                        'gender' => $volunteer->gender,
                        'city' => ['id' => 2, 'name_en' => 'New Taipei City'],
                        'address' => $volunteer->address,
                        'phone_number' => $volunteer->phone_number,
                        'email' => $volunteer->email,
                        'emergency_contact' => $volunteer->emergency_contact,
                        'emergency_phone' => '0910123456',
                        'introduction' => 'Hi, my name is XXX',
                        'experiences' => ['href' => env('APP_URL') . '/api/users/me/experiences'],
                        'educations' => ['href' => env('APP_URL') . '/api/users/me/educations'],
                        'skills' => [
                            [
                                'name' => 'Swimming',
                                'id' => 1,
                            ],
                            [
                                'name' => 'Programming',
                                'id' => 2,
                            ]
                        ],
                        'equipment' => [
                            [
                                'name' => 'Car',
                                'id' => 1,
                            ],
                            [
                                'name' => 'Scooter',
                                'id' => 2,
                            ],
                            [
                                'name' => 'Camera',
                                'id' => 3,
                            ]
                        ],
                        'projects' => [
                            'href' => env('APP_URL') . '/api/users/me/projects'
                        ],
                        'processes' => [
                            'participating_number' => 0,
                            'participated_number' => 0,
                            'href' => env('APP_URL') . '/api/users/me/processes'
                        ],
                        'avatar_url' => config('vms.avatarHost') . '/' . config('vms.avatarRootPath') . '/' . $volunteer->avatar_path,
                        'is_actived' => $volunteer->is_actived
                    ])
             ->assertResponseStatus(200);
    }

    public function testFailedUpdateMe()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $originalUsername = $volunteer->username;
        $volunteer->is_actived = true;
        $volunteer->save();

        $skills = ['Swimming', 'Programming'];
        $equipment = ['Car', 'Scooter', 'Camera'];

        foreach ($skills as $skill) {
            $volunteer->skills()
                 ->firstOrCreate(['name' => $skill]);
        }

        foreach ($equipment as $eq) {
            $volunteer->equipment()
                 ->firstOrCreate(['name' => $eq]);
        }

        $token = JWTAuth::fromUser($volunteer);
        $putData = [
            'username' => 'qoo',
            'emergency_phone' => '0910123456'
        ];

        $this->json('put',
                    '/api/users/me', $putData, [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ])
             ->assertResponseStatus(200);
        $this->seeInDatabase('volunteers', ['username' => $originalUsername]);
    }

    public function testUploadAvatarMe()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $originalUsername = $volunteer->username;
        $volunteer->is_actived = true;
        $volunteer->save();

        $token = JWTAuth::fromUser($volunteer);

        $avatarPath = $this->exampleRoot . '/default-photo.png';
        $avatarType = pathinfo($avatarPath, PATHINFO_EXTENSION);
        $avatarFileName = 'avatar123.' . $avatarType;

        StringUtil::shouldReceive('generateHashToken')
                        ->once()
                        ->andReturn('avatar123');
        
        $fileSystemMock = Mockery::mock('\Illuminate\Contracts\Filesystem\Filesystem');
        $fileSystemMock->shouldReceive('put')->once()->andReturn(true);
        Storage::shouldReceive('disk')
                      ->once()
                      ->with('avatar')
                      ->andReturn($fileSystemMock);

        $putData = [
            'avatar' => 'data:image/' . $avatarType . ';base64,' . base64_encode(file_get_contents($avatarPath)),
            'skip_profile' => true
        ];


        $responseJson = $this->json('post',
                    '/api/users/me/avatar', $putData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ]);

        $responseJson->seeJsonEquals([
                        'avatar_url' => config('vms.avatarHost') . '/' . config('vms.avatarRootPath') . '/' . $avatarFileName,
                        'avatar_name' => $avatarFileName
                    ]
                )
             ->assertResponseStatus(200);
    }

    public function testUploadAvatar()
    {
        $this->factoryModel();
        
        $avatarPath = $this->exampleRoot . '/default-photo.png';
        $avatarType = pathinfo($avatarPath, PATHINFO_EXTENSION);
        $putData = [
            'avatar' => 'data:image/' . $avatarType . ';base64,' . base64_encode(file_get_contents($avatarPath))
        ];

        $avatarFileName = 'avatar123.' . $avatarType;

        StringUtil::shouldReceive('generateHashToken')
                        ->once()
                        ->andReturn('avatar123');
        
        $fileSystemMock = Mockery::mock('\Illuminate\Contracts\Filesystem\Filesystem');
        $fileSystemMock->shouldReceive('put')->once()->andReturn(true);

        Storage::shouldReceive('disk')
                      ->once()
                      ->with('avatar')
                      ->andReturn($fileSystemMock);

        $responseJson = $this->json('post',
                    '/api/avatar', $putData,
                    [
                        'X-VMS-API-Key' => $this->getApiKey()
                    ]);

        $responseJson->seeJsonEquals([
                        'avatar_url' => config('vms.avatarHost') . '/' . config('vms.avatarRootPath') . '/' . $avatarFileName,
                        'avatar_name' => $avatarFileName
                    ]
                )
             ->assertResponseStatus(200);
    }

    public function testDeleteMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;
        $volunteer->avatar_path = 'avatar123.png';
        $volunteer->save();

        $token = JWTAuth::fromUser($volunteer);
        $putData = [
            'username' => $volunteer->username,
            'password' => 'ThisIsMyPassW0Rd'
        ];

        $fileSystemMock = Mockery::mock('\Illuminate\Contracts\Filesystem\Filesystem');
        $fileSystemMock->shouldReceive('delete')
                       ->once()
                       ->with('avatar123.png')
                       ->andReturn(true);

        Storage::shouldReceive('disk')
                      ->once()
                      ->with('avatar')
                      ->andReturn($fileSystemMock);

        $this->json('post',
                    '/api/users/me/delete', $putData, [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ])
             ->assertResponseStatus(204);
        $this->missingFromDatabase('volunteers', ['username' => $volunteer->username]);
    }

    public function testFailedDeleteMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;
        $volunteer->save();

        $token = JWTAuth::fromUser($volunteer);
        $putData = [
            'username' => $volunteer->username,
            'password' => 'MyWrongPassword'
        ];

        $this->json('post',
                    '/api/users/me/delete', $putData, [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->getApiKey()
                    ])
             ->assertResponseStatus(401);
        $this->seeInDatabase('volunteers', ['username' => $volunteer->username]);
    }
}
