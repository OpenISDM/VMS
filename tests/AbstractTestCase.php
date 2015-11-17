<?php

abstract class AbstractTestCase extends TestCase
{
    protected $unauthoirzedHeader;
    protected $cities;

    public function __construct()
    {
        $this->unauthoirzedHeader = [
            'X-VMS-API-Key' => $this->getApiKey()
        ];
    }

    protected function getApiKey()
    {
        return '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1';
    }

    protected function factoryModel()
    {
        factory(App\ApiKey::class)->create([
            'api_key' => $this->getApiKey()
        ]);

        $this->cities = factory(App\City::class, 'testCity', 20)
            ->make()
            ->each(function ($city) {
                $city->country()->associate(factory(App\Country::class, 'testCountry')->create());
                $city->save();
            });
    }

    protected function beActiveVolunteer()
    {
        $this->volunteer = factory(App\Volunteer::class)->create(
            [
                'is_actived' => true
            ]
        );
    }

    protected function getHeaderWithAuthorization()
    {
        $token = JWTAuth::fromUser($this->volunteer);
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'X-VMS-API-Key' => $this->getApiKey()
        ];
    }

    protected function getHeaderOnlyWithApiKey()
    {
        return [
            'X-VMS-API-Key' => $this->getApiKey()
        ];
    }
}
