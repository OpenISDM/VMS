<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\ExceedingIndexException;

class VolunteerProfileControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $apiKey;
    protected $headerArray = [];

    public function setUp()
    {
        parent::setUp();

        $this->apiKey = '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1';
        $this->headerArray = [
            'X-VMS-API-Key' => $this->apiKey
        ];

        //$this->postData = json_decode(file_get_contents(__DIR__ . '/examples/register_post.json'), true);
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
                        'X-VMS-API-Key' => $this->apiKey
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
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals([
                        'message' => 'Unable to execute',
                        'errors' => [[
                            'code' => 'exceeding_index_value'
                        ]]
                    ])
             ->assertResponseStatus(400);
    }

    public function testSuccessfullyUpdateEqiupmentMe()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $postData = [
            'equipment' => [
                'Car',
                'Scooter',
                'Camera'
            ],
            'existing_equipment_indexes' => [
            ]
        ];

        $this->json('post',
                    '/api/users/me/equipment',
                    $postData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(204);

        $testVolunter = App\Volunteer::find($volunteer->id);

        foreach ($postData['equipment'] as $equipment) {
            $testEquipment = $testVolunter->equipment()->where('name', $equipment)->first();
            $this->assertEquals($equipment, $testEquipment->name);
        }
    }

    public function testUpdateEquipmentMeExceedingIndexException()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $postData = [
            'equipment' => [
                'Car',
                'Scooter',
                'Camera'
            ],
            'existing_equipment_indexes' => [
                0,
                1,
                2,
                3,
                4
            ]
        ];

        foreach ($postData['equipment'] as $equipment) {
            $volunteer->skills()->create(['name' => $equipment]);
        }

        $this->json('post',
                    '/api/users/me/equipment',
                    $postData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals([
                        'message' => 'Unable to execute',
                        'errors' => [[
                            'code' => 'exceeding_index_value'
                        ]]
                    ])
             ->assertResponseStatus(400);
    }

    public function testSuccessfullyStoreEductionMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $postData = [
            'school' => 'NCKU',
            'degree' => 5,
            'field_of_study' => 'Computer Science',
            'start_year' => 2012,
            'end_year' => 2014
        ];

        $this->json('post', '/api/users/me/educations', $postData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals(['education' => ['id' => 1]])
             ->assertResponseStatus(201);
    }

    public function testSuccessfullyUpdateEducationMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $education = factory(App\Education::class)->make();
        $volunteer->educations()->save($education);

        $token = JWTAuth::fromUser($volunteer);

        $putData = [
            'id' => $education->id,
            'school' => 'NCKU',
            'degree' => 4,
            'field_of_study' => 'Computer Science',
            'start_year' => 2012,
            'end_year' => 2014
        ];

        $this->json('put', '/api/users/me/educations', $putData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(204);

        $this->seeInDatabase('educations', ['id' => $education->id, 'degree' => 4]);
    }

    public function testUpdateEducationMeAccessDeniedException()
    {
        $this->factoryModel();

        // Create volunteer A 
        $volunteerA = factory(App\Volunteer::class)->create();
        $volunteerA->is_actived = true;

        $educationA = factory(App\Education::class)->make();
        $volunteerA->educations()->save($educationA);

        // Create volunteer B
        $volunteerB = factory(App\Volunteer::class)->create();
        $volunteerB->is_actived = true;

        $educationB = factory(App\Education::class)->make(
                            [
                                'school' => 'MIT',
                                'degree' => 6,
                                'field_of_study' => 'Artificial Intelligence',
                                'start_year' => '2008',
                                'end_year' => '2013'
                            ]
                        );
        $volunteerB->educations()->save($educationB);

        $token = JWTAuth::fromUser($volunteerA);
        $putData = [
            'id' => $educationB->id,
            'school' => 'MIT',
            'degree' => 6,
            'field_of_study' => 'Artificial Intelligence (AI)',
            'start_year' => '2008',
            'end_year' => '2013'
        ];

        $this->json('put', '/api/users/me/educations', $putData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals([
                    'message' => 'Not have right to access',
                    'errors' => [[
                        'code' => 'cannot_access'
                    ]]
                ])
             ->assertResponseStatus(403);
    }

    public function testSuccessfullyDeleteEducationMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $education = factory(App\Education::class)->make();
        $volunteer->educations()->save($education);

        $token = JWTAuth::fromUser($volunteer);

        $this->json('delete',
                    '/api/users/me/educations/' . $education->id, [], [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(204);
        $this->notSeeInDatabase('educations', ['id' => $education->id]);
    }

    public function testSuccessfullyShowEducationMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $education = factory(App\Education::class)->make();
        $volunteer->educations()->save($education);

        $token = JWTAuth::fromUser($volunteer);

        $this->json('get',
                    '/api/users/me/educations', [], [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals([
                    'educations' => [[
                        'id' => $education->id,
                        'school' => $education->school,
                        'degree' => $education->degree,
                        'field_of_study' => $education->field_of_study,
                        'start_year' => $education->start_year,
                        'end_year' => $education->end_year
                    ]]
                ])
             ->assertResponseStatus(200);
    }

    public function testSuccessfullyStoreExperienceMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $postData = [
            'company' => 'Academia Sinica',
            'job_title' => 'Research Assistant',
            'start_year' => 2014,
        ];

        $this->json('post', '/api/users/me/experiences', $postData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals(['experience' => ['id' => 1]])
             ->assertResponseStatus(201);
    }

    public function testSuccessfullyUpdateExperienceMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $experience = factory(App\Experience::class)->make();
        $volunteer->experiences()->save($experience);

        $token = JWTAuth::fromUser($volunteer);

        $putData = [
            'id' => $experience->id,
            'company' => 'Academia Sinica',
            'job_title' => 'Research Assistant',
            'start_year' => 2014,
            'end_year' => 2016
        ];

        $this->json('put', '/api/users/me/experiences', $putData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(204);

        $this->seeInDatabase('experiences', ['id' => $experience->id, 'end_year' => 2016]);
    }

    public function testUpdateExperienceMeAccessDeniedException()
    {
        $this->factoryModel();

        // Create volunteer A 
        $volunteerA = factory(App\Volunteer::class)->create();
        $volunteerA->is_actived = true;

        $experienceA = factory(App\Experience::class)->make();
        $volunteerA->experiences()->save($experienceA);

        // Create volunteer B
        $volunteerB = factory(App\Volunteer::class)->create();
        $volunteerB->is_actived = true;

        $experienceB = factory(App\Experience::class)->make(
                            [
                                'company' => 'Orz',
                                'job_title' => 'CEO',
                                'start_year' => 2010,
                            ]
                        );
        $volunteerB->experiences()->save($experienceB);

        $token = JWTAuth::fromUser($volunteerA);
        $putData = [
            'id' => $experienceB->id,
            'company' => 'Orz',
            'job_title' => 'CEO',
            'start_year' => 2010,
            'end_year' => 2015
        ];

        $this->json('put', '/api/users/me/experiences', $putData,
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals([
                    'message' => 'Not have right to access',
                    'errors' => [[
                        'code' => 'cannot_access'
                    ]]
                ])
             ->assertResponseStatus(403);
    }

    public function testSuccessfullyDeleteExperienceMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $experience = factory(App\Experience::class)->make();
        $volunteer->experiences()->save($experience);

        $token = JWTAuth::fromUser($volunteer);

        $this->json('delete',
                    '/api/users/me/experiences/' . $experience->id, [], [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(204);
        $this->notSeeInDatabase('experiences', ['id' => $experience->id]);
    }

    public function testSuccessfullyShowExperienceMe()
    {
        $this->factoryModel();

        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $experience = factory(App\Experience::class)->make();
        $volunteer->experiences()->save($experience);

        $token = JWTAuth::fromUser($volunteer);

        $this->json('get',
                    '/api/users/me/experiences', [], [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJsonEquals([
                    'experiences' => [[
                        'id' => $experience->id,
                        'company' => 'Academia Sinica',
                        'job_title' => 'Research Assistant',
                        'start_year' => 2014,
                    ]]
                ])
             ->assertResponseStatus(200);
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
                        'X-VMS-API-Key' => $this->apiKey
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
                        'avatar_url' => env('APP_URL') . '/upload/image/avatar/' . $volunteer->avatar_path,
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
                        'X-VMS-API-Key' => $this->apiKey
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
                        'avatar_url' => env('APP_URL') . '/upload/image/avatar/' . $volunteer->avatar_path,
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
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->assertResponseStatus(200);
        $this->seeInDatabase('volunteers', ['username' => $originalUsername]);
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
