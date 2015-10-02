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
