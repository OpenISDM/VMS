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

Route::any('/', function()
{
    return "Hello World!";
});



Route::get('user/{id}', 'UserController@showProfile');

Route::post('user/{id}/edit', 'UserController@editProfile');