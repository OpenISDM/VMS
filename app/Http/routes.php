<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dingo API Routes
|--------------------------------------------------------------------------
|
|
*/

$api = app('Dingo\Api\Routing\Router');

/**
 * TODO: need to add jwt-auth middlewares
 */

// Version 1.0
$api->version('v1.0', function ($api) {
    // Public routing group
    $api->group(['middleware' => 'check.header'], function ($api) {
        $api->post('register', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@register');
        $api->post('auth', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@login');
        $api->delete('auth',
                  'App\Http\Controllers\Api\V1_0\VolunteerAuthController@logout');
        $api->get('/users/me', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@showMe');
        $api->post('/users/me/skills', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateSkillsMe');
        $api->post('/users/me/equipment', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateEquipmentMe');
        $api->post('/users/me/educations', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@storeEducationMe');
        $api->put('/users/me/educations', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateEducationMe');
        $api->delete('/users/me/educations/{education_id}', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@deleteEducationMe');
        $api->get('email_verification/{email_address}/{verification_code}',
                  'App\Http\Controllers\Api\V1_0\VolunteerAuthController@emailVerification');
    });
});
