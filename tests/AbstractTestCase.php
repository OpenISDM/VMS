<?php

abstract class AbstractTestCase extends TestCase
{
    protected function getApiKey()
    {
        return '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1';
    }

    protected function factoryModel()
    {
        factory(App\ApiKey::class)->create([
            'api_key' => $this->getApiKey()
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
