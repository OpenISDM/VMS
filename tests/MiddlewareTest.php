<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MiddlewareTest extends TestCase
{
    use DatabaseMigrations;

    protected $apiKey;
    protected $postData;

    public function setUp()
    {
        parent::setUp();

        $this->apiKey = '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1';
        $this->postData = json_decode(file_get_contents(__DIR__ . '/examples/register_post.json'), true);
    }

    /*
    public function testContentTypeCheck()
    {
        $this->post('/api/register', [])
             ->seeJsonEquals([
                'message' => 'Content-Type is unmatched',
                'errors' => [['code' => 'unmatched_content_type']]
               ])
             ->assertResponseStatus(400);
    }
    */

    public function testApiKeyCheck()
    {
        $this->factoryModel();

        $this->json('post', '/api/register', $this->postData, ['Content-Type'=> 'application/json'])
             ->seeJsonEquals([
                'message' => 'API key is not validated',
                'errors' => [['code' => 'incorrect_api_key']],
               ])
             ->assertResponseStatus(401);
    }

    public function testPassMiddleware()
    {
        $this->factoryModel();
        $this->expectsJobs(App\Jobs\SendVerificationEmail::class);
        $headerArray = [
            'X-VMS-API-Key' => $this->apiKey
        ];

        $this->json('post', '/api/register', $this->postData, $headerArray)
             ->assertResponseStatus(201);
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
