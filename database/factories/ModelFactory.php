<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\ApiKey::class, function (Faker\Generator $faker) {
    return [
        'api_key' => '581dba93a4dbafa42a682d36b015d8484622f8e3543623bec5a291f67f5ddff1'
    ];
});

$factory->define(App\Country::class, function (Faker\Generator $faker) {
    return [
        'name' =>'Taiwan'
    ];
});

$factory->define(App\City::class, function (Faker\Generator $faker) {
    return [
        'name' => 'Taipei City'
    ];
});

$factory->define(App\Volunteer::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->userName,
        'password' => bcrypt('ThisIsMyPassW0Rd'),
        'first_name' => $faker->firstNameMale,
        'last_name' => $faker->lastName,
        'birth_year' => 1991,
        'gender' => 'male',
        'city_id' => 1,
        'address' => $faker->address,
        'phone_number' => $faker->phoneNumber,
        'email' => $faker->email,
        'emergency_contact' => $faker->name(),
        'emergency_phone' => $faker->phoneNumber,
        'avatar_path' => $faker->username . '001.png',
        'introduction' => 'Hi, my name is XXX'
    ];
});

$factory->define(App\VerificationCode::class, function (Faker\Generator $fake) {
    return [
    ];
});

$factory->define(App\Education::class, function (Faker\Generator $faker) {
    return [
        'school' => 'NCKU',
        'degree' => 5,
        'field_of_study' => 'Computer Science',
        'start_year' => 2012,
        'end_year' => 2014
    ];
});

$factory->define(App\Experience::class, function (Faker\Generator $faker) {
    return [
        'company' => 'Academia Sinica',
        'job_title' => 'Research Assistant',
        'start_year' => 2014,
        'end_year' => null
    ];
});


