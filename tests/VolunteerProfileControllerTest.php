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

    /**
     * A basic test example.
     *
     * @return void
     */
    public function DISABLEtestShowMe()
    {
        $this->factoryModel();
        $volunteer = factory(App\Volunteer::class)->create();
        $volunteer->is_actived = true;

        $token = JWTAuth::fromUser($volunteer);

        $this->json('get',
                    '/api/users/me',
                    [],
                    [
                        'Authorization' => 'Bearer ' . $token,
                        'X-VMS-API-Key' => $this->apiKey
                    ])
             ->seeJson([
                'username' => $this->postData['username']
             ])
             ->assertResponseStatus(204);
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
             //->seeJson([
             //   'username' => 'a'
             //])
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
             //->seeJson([
             //   'username' => 'a'
             //])
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
