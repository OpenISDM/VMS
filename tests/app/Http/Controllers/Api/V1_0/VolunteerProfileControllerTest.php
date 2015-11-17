<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class VolunteerProfileControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

    protected $apiKey;
    protected $exampleRoot;

    public function setUp()
    {
        parent::setUp();

        $this->exampleRoot = dirname(__FILE__).'/../../../../../examples';
    }

    public function testSuccessfullyUpdateSkillsMe()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $postData = [
            'skills' => [
                'Swimming',
                'Programming',
                'Repo rescue',
            ],
            'existing_skill_indexes' => [
            ],
        ];

        $this->json(
            'post',
            '/api/users/me/skills',
            $postData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(204);

        $testVolunter = App\Volunteer::find($this->volunteer->id);

        foreach ($postData['skills'] as $skill) {
            $testSkill = $testVolunter->skills()->where('name', $skill)->first();
            $this->assertEquals($skill, $testSkill->name);
        }
    }

    public function testUpdateSkillsMeExceedingIndexException()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $postData = [
            'skills' => [
                'Swimming',
                'Programming',
                'Repo rescue',
            ],
            'existing_skill_indexes' => [
                1,
                2,
                3,
                4,
            ],
        ];

        foreach ($postData['skills'] as $skill) {
            $this->volunteer->skills()->create(['name' => $skill]);
        }

        $this->json(
            'post',
            '/api/users/me/skills',
            $postData,
            $this->getHeaderWithAuthorization()
        )->seeJsonEquals([
            'message' => 'Unable to execute',
            'errors' => [[
                'code' => 'exceeding_index_value',
            ]],
        ])->assertResponseStatus(400);
    }

    public function testShowMe()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();
        $this->createSkillsAndEquipment();

        $this->json(
            'get',
            '/api/users/me',
            [],
            $this->getHeaderWithAuthorization()
        )->seeJson($this->getProfileDetail())
         ->assertResponseStatus(200);
    }

    public function testUpdateMe()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();
        $this->createSkillsAndEquipment();

        $putData = [
            'city' => ['id' => 1],
            'emergency_phone' => '0910123456',
        ];

        $city = $this->cities[0];

        //var_dump($cityName);

        $this->json(
            'put',
            '/api/users/me',
            $putData,
            $this->getHeaderWithAuthorization()
        )->seeJson([
            'city' => ['id' => 1, 'name_en' => $city->name]
        ])
        ->seeJson([
            'emergency_phone' => '0910123456'
        ])
        ->assertResponseStatus(200);
    }

    public function testFailedUpdateMe()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $originalUsername = $this->volunteer->username;

        $skills = ['Swimming', 'Programming'];
        $equipment = ['Car', 'Scooter', 'Camera'];

        foreach ($skills as $skill) {
            $this->volunteer->skills()
                 ->firstOrCreate(['name' => $skill]);
        }

        foreach ($equipment as $eq) {
            $this->volunteer->equipment()
                 ->firstOrCreate(['name' => $eq]);
        }

        $putData = [
            'username' => 'qoo',
            'emergency_phone' => '0910123456',
        ];

        $this->json(
            'put',
            '/api/users/me',
            $putData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(200);
        $this->seeInDatabase('volunteers', ['username' => $originalUsername]);
    }

    public function testUploadAvatarMe()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $avatarPath = $this->exampleRoot.'/default-photo.png';
        $avatarType = pathinfo($avatarPath, PATHINFO_EXTENSION);
        $avatarFileName = 'avatar123.'.$avatarType;

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
            'avatar' => 'data:image/'.$avatarType.';base64,'.base64_encode(file_get_contents($avatarPath)),
            'skip_profile' => true,
        ];

        $responseJson = $this->json(
            'post',
            '/api/users/me/avatar',
            $putData,
            $this->getHeaderWithAuthorization()
        )->seeJsonEquals(
            [
                'avatar_url' => config('vms.avatarHost').'/'.config('vms.avatarRootPath').'/'.$avatarFileName,
                'avatar_name' => $avatarFileName,
            ]
        )->assertResponseStatus(200);
    }

    public function testUploadAvatar()
    {
        $this->factoryModel();

        $avatarPath = $this->exampleRoot.'/default-photo.png';
        $avatarType = pathinfo($avatarPath, PATHINFO_EXTENSION);
        $putData = [
            'avatar' => 'data:image/'.$avatarType.';base64,'.base64_encode(file_get_contents($avatarPath)),
        ];

        $avatarFileName = 'avatar123.'.$avatarType;

        StringUtil::shouldReceive('generateHashToken')
                        ->once()
                        ->andReturn('avatar123');

        $fileSystemMock = Mockery::mock('\Illuminate\Contracts\Filesystem\Filesystem');
        $fileSystemMock->shouldReceive('put')->once()->andReturn(true);

        Storage::shouldReceive('disk')
                      ->once()
                      ->with('avatar')
                      ->andReturn($fileSystemMock);

        $responseJson = $this->json(
            'post',
            '/api/avatar',
            $putData,
            $this->getHeaderOnlyWithApiKey()
        );

        $responseJson->seeJsonEquals(
            [
                'avatar_url' => config('vms.avatarHost').'/'.config('vms.avatarRootPath').'/'.$avatarFileName,
                'avatar_name' => $avatarFileName,
            ]
        )->assertResponseStatus(200);
    }

    public function testDeleteMe()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $this->volunteer->avatar_path = 'avatar123.png';
        $this->volunteer->save();

        $putData = [
            'username' => $this->volunteer->username,
            'password' => 'ThisIsMyPassW0Rd',
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

        $this->json(
            'post',
            '/api/users/me/delete',
            $putData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(204);

        $this->missingFromDatabase('volunteers', ['username' => $this->volunteer->username]);
    }

    public function testFailedDeleteMe()
    {
        $this->factoryModel();
        $this->beActiveVolunteer();

        $putData = [
            'username' => $this->volunteer->username,
            'password' => 'MyWrongPassword',
        ];

        $this->json(
            'post',
            '/api/users/me/delete',
            $putData,
            $this->getHeaderWithAuthorization()
        )->assertResponseStatus(401);

        $this->seeInDatabase('volunteers', ['username' => $this->volunteer->username]);
    }

    private function getProfileDetail()
    {
        $equipment = $this->volunteer->equipment()->get();
        $skills = $this->volunteer->skills()->get();

        return [
            'username' => $this->volunteer->username,
            'first_name' => $this->volunteer->first_name,
            'last_name' => $this->volunteer->last_name,
            'birth_year' => $this->volunteer->birth_year,
            'gender' => $this->volunteer->gender,
            'city' => ['id' => $this->volunteer->city->id, 'name_en' => $this->volunteer->city->name],
            'address' => $this->volunteer->address,
            'phone_number' => $this->volunteer->phone_number,
            'email' => $this->volunteer->email,
            'emergency_contact' => $this->volunteer->emergency_contact,
            'emergency_phone' => $this->volunteer->emergency_phone,
            'introduction' => 'Hi, my name is XXX',
            'experiences' => ['href' => env('APP_URL').'/api/users/me/experiences'],
            'educations' => ['href' => env('APP_URL').'/api/users/me/educations'],
            'skills' => $skills,
            'equipment' => $equipment,
            'projects' => [
                'href' => env('APP_URL').'/api/users/me/projects',
            ],
            'processes' => [
                'participating_number' => 0,
                'participated_number' => 0,
                'href' => env('APP_URL').'/api/users/me/processes',
            ],
            'avatar_url' => config('vms.avatarHost').'/'.config('vms.avatarRootPath').'/'.$this->volunteer->avatar_path,
            'is_actived' => $this->volunteer->is_actived,
        ];
    }

    private function createSkillsAndEquipment()
    {
        factory(App\Skill::class, 3)
            ->create()
            ->each(function ($skill) {
                $skill->volunteers()->save($this->volunteer);
            });

        factory(App\Equipment::class, 3)
            ->create()
            ->each(function ($equipment) {
                $equipment->volunteers()->save($this->volunteer);
            });
    }
}
