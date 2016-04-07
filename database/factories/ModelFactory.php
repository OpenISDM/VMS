<?php
use Faker\Generator;
use App\Hyperlink;
use App\ProjectCustomField;
use App\CustomField\RadioButtonMetadata;

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

$factory->defineAs(App\City::class, 'testCity', function (Faker\Generator $faker) {
    return [
        'name' => $faker->city
    ];
});


$factory->defineAs(App\Country::class, 'testCountry', function (Faker\Generator $faker) {
    return [
        'name' => $faker->country
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

$factory->define(App\Skill::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(App\Equipment::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word
    ];
});

$factory->define(App\Project::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(),
        'organization' => $faker->sentence(),
        'is_published' => true,
        'permission' => 0,
    ];
});

$factory->define(App\Hyperlink::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'link' => $faker->url
    ];
});

$factory->defineAs(App\Project::class, 'project_private_for_user', function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(),
        'organization' => $faker->sentence(),
        'is_published' => true,
        'permission' => 1,
    ];
});

$factory->defineAs(App\Project::class, 'project_private_for_member', function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(),
        'organization' => $faker->sentence(),
        'is_published' => true,
        'permission' => 2,
    ];
});

$factory->define(App\ProjectCustomField::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->paragraph(),
        'required' => true,
        'type' => 'RADIO_BUTTON',
        'order' => 1,
        'metadata' => new RadioButtonMetadata([
            'options' => [
                [
                    'value' => 0,
                    'display_name' => 'qoo',
                ],
                [
                    'value' => 1,
                    'display_name' => 'foo',
                ],
            ]
        ]),
        'is_published' => true,
    ];
});
