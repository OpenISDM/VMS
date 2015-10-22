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

// empty


/*
|--------------------------------------------------------------------------
| Dingo API Routes
|--------------------------------------------------------------------------
|
| URL Prefix: /api
| Example: /api/register
|
*/

$api = app('Dingo\Api\Routing\Router');

// Version 1.0
$api->version('v1.0', function ($api) {
    
    /*
    |--------------------------------------------------------------------------
    | Public endpoints 
    |--------------------------------------------------------------------------
    | 
    | The request MUST contain API key in header.
    |
    */
    $api->group(['middleware' => 'check.header'], function ($api) {
        
        // Register
        $api->post('register', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@register');

        // Upload new avatar
        $api->post('avatar', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@uploadAvatar');

        // Login
        $api->post('auth', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@login');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Protected endpoints 
    |--------------------------------------------------------------------------
    | 
    | The request MUST contain API Key and JWT in header.
    |
    */
    $api->group(['middleware' => ['check.header', 'api.auth']], function ($api) {

        // logout
        $api->delete('auth',
            'App\Http\Controllers\Api\V1_0\VolunteerAuthController@logout');

        // delete volunteer's own account
        $api->post('/users/me/delete',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@deleteMe');

        // Email address validation
        $api->get('email_verification/{email_address}/{verification_code}',
            'App\Http\Controllers\Api\V1_0\VolunteerAuthController@emailVerification');

        // Retrive volunteer's profile
        $api->get('/users/me', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@showMe');

        // Update volunteer's profile
        $api->put('/users/me', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateMe');

        // Upload volunteer's avatar image
        $api->post('/users/me/avatar', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@uploadAvatarMe');
        
        // Update skills
        $api->post('/users/me/skills',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateSkillsMe');
        
        // Update equipment
        $api->post('/users/me/equipment',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateEquipmentMe');
        
        // Experience CRUD
        $api->get('/users/me/experiences',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@show');
        $api->post('/users/me/experiences',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@store');
        $api->put('/users/me/experiences',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@update');
        $api->delete('/users/me/experiences/{id}',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@destroy');

        // Educations CRUD
        $api->get('/users/me/educations',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@show');
        $api->post('/users/me/educations',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@store');
        $api->put('/users/me/educations',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@update');
        $api->delete('/users/me/educations/{id}',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@destroy');
        
    });
});
