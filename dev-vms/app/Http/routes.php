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

Route::any('/', function () {
    return "Hello World!";
});

Route::model('processes', 'Process');
Route::model('projects', 'Project');

Route::bind('processes', function($value, $route) {
	return App\Process::whereSlug($value)->first();
});
Route::bind('projects', function($value, $route) {
	return App\Project::whereSlug($value)->first();
});

Route::resource('projects.processes', 'ProcessesController');
Route::resource('projects', 'ProjectsController');

Route::get('user/{id}', 'EditProfileController@showProfile');
Route::post('user/{id}/edit', 'EditProfileController@editProfile');

