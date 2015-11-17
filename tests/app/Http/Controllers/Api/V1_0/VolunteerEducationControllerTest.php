<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VolunteerEducationControllerTest extends AbstractTestCase
{
    use DatabaseMigrations;

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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
                        'X-VMS-API-Key' => $this->getApiKey()
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
}
