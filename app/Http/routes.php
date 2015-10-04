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

// Version 1.0
$api->version('v1.0', function ($api) {
    // Public routing group
    $api->group(['middleware' => 'check.header'], function ($api) {
        $api->post('register', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@register');
        $api->post('auth', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@login');
        $api->get('email_verification/{email_address}/{verification_code}', 
                  'App\Http\Controllers\Api\V1_0\VolunteerAuthController@emailVerification');       
    });
});


