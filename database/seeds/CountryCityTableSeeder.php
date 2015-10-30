<?php

use Illuminate\Database\Seeder;
use App\Country;
use App\City;
use Illuminate\Database\Eloquent\Model;

/**
 * A database seed for insert new countries and cities
 */
class CountryCityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $seedData = [
            'Taiwan' => [
                'Taipei City',
                'New Taipei City',
                'Taoyuan City',
                'Taichung City',
                'Kaohsiung City',
                'Tainan City',
                'Hsinchu City',
                'Chiayi City',
                'Keelung City',
                'Hsinchu County',
                'Miaoli County',
                'Changhua County',
                'Nantou County',
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

        foreach ($seedData as $countryName => $cityList) {
            $country = Country::create(['name' => $countryName]);

            foreach ($cityList as $cityName) {
                $country->cities()->create(['name' => $cityName]);
            }
        }

        Model::reguard();
    }
}
