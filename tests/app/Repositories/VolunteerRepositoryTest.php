<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Repositories\VolunteerRepository;
use App\Repositories\CityRepository;

class VolunteerRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreate()
    {
        $this->factoryCities();

        $cityRepository = new CityRepository();
        $city = $cityRepository->findById(1);

        $data = array(
            'username' => 'user1',
            'password' => 'password01',
            'first_name' => 'huang',
            'last_name' => 'aming',
            'birth_year' => '1991',
            'gender' => 'male',
            'city' => $city,
            'location' => 'Taipei city',
            'phone_number' => '0910123456',
            'email' => 'aming@abc.com',
            'emergency_contact' => 'Qoo',
            'emergency_phone' => '0988123456',
            'avatar_path' => 'abc.png'
        );

        $volunteerRepository = new VolunteerRepository();
        $volunteer = $volunteerRepository->create($data);

        $this->assertEquals(1, $volunteer->city_id);
        $this->assertEquals('abc.png', $volunteer->avatar_path);
        $this->seeInDatabase('volunteers', ['username' => 'user1', 'avatar_path' => 'abc.png']);
    }

    protected function factoryCities()
    {
        factory(App\City::class, 'testCity', 3)
            ->make()
            ->each(function ($u) {
                $u->country()->associate(factory(App\Country::class)->create());
                $u->save();
            });
    }
}
