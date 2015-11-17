<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VolunteerExperienceControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
}
