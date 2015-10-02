<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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

    public function factoryModel()
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
