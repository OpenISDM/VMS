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
    $api->group(['middleware' => ['check.header']], function ($api) {

        // Register
        $api->post('register', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@register');

        // Upload new avatar
        $api->post('avatar', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@uploadAvatar');

        // Login
        $api->post('auth', 'App\Http\Controllers\Api\V1_0\VolunteerAuthController@login');

        // Create a password reset request
        $api->post('users/password_reset',
            'App\Http\Controllers\Api\V1_0\VolunteerPasswordController@createPasswordReset');

        // Reset password
        $api->put('users/password_reset/{email_address}/{reset_password_token}',
            'App\Http\Controllers\Api\V1_0\VolunteerPasswordController@postPasswordReset');

        // Get skill candidiates
        $api->get('skill_candidates/{keyword}',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@getSkillCandidatedKeywords');

        // Get equipment candidates
        $api->get('equipment_candidates/{keyword}',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@getEquipmentCandidatedKeywords');

        $api->get('projects/{id}',
            'App\Http\Controllers\Api\V1_0\ProjectController@show');
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
        $api->post('users/me/delete',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@deleteMe');

        // Change password
        $api->put('users/me/password',
            'App\Http\Controllers\Api\V1_0\VolunteerPasswordController@postChangePassword');

        // Email address validation
        $api->get('email_verification/{email_address}/{verification_code}',
            'App\Http\Controllers\Api\V1_0\VolunteerAuthController@emailVerification');

        $api->post('resend_email_verification',
            'App\Http\Controllers\Api\V1_0\VolunteerAuthController@resendEmailVerification');

        // Retrive volunteer's profile
        $api->get('users/me', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@showMe');

        // Update volunteer's profile
        $api->put('users/me', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateMe');

        // Upload volunteer's avatar image
        $api->post('users/me/avatar', 'App\Http\Controllers\Api\V1_0\VolunteerProfileController@uploadAvatarMe');

        // Update skills
        $api->post('users/me/skills',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateSkillsMe');
        // Get skills
        $api->get('users/me/skills',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@getSkillsMe');

        // Update equipment
        $api->post('users/me/equipment',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@updateEquipmentMe');
        // Get skills
        $api->get('users/me/equipment',
            'App\Http\Controllers\Api\V1_0\VolunteerProfileController@getEquipmentMe');

        // Experience CRUD
        $api->get('users/me/experiences',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@show');
        $api->post('users/me/experiences',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@store');
        $api->put('users/me/experiences',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@update');
        $api->delete('users/me/experiences/{id}',
            'App\Http\Controllers\Api\V1_0\VolunteerExperienceController@destroy');

        // Educations CRUD
        $api->get('users/me/educations',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@show');
        $api->post('users/me/educations',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@store');
        $api->put('users/me/educations',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@update');
        $api->delete('users/me/educations/{id}',
            'App\Http\Controllers\Api\V1_0\VolunteerEducationController@destroy');

        // Projects CRUD
        $api->post('projects',
            'App\Http\Controllers\Api\V1_0\ProjectController@store');
        $api->put('projects/{id}',
            'App\Http\Controllers\Api\V1_0\ProjectController@update');
        $api->get('projects',
            'App\Http\Controllers\Api\V1_0\ProjectController@showAll');
        $api->get('managed_projects',
            'App\Http\Controllers\Api\V1_0\ProjectController@showManagedProjects');
        $api->get('attending_projects',
            'App\Http\Controllers\Api\V1_0\ProjectController@showSelfAttendingProjects');

        // Project members
        $api->post('projects/{id}/members',
            'App\Http\Controllers\Api\V1_0\ProjectController@attachVolunteer');
        $api->get('projects/{projectId}/members',
            'App\Http\Controllers\Api\V1_0\ProjectController@showMembers');
        $api->delete('projects/{projectId}/members/{userId}',
            'App\Http\Controllers\Api\V1_0\ProjectController@detachVolunteer');
        $api->post('projects/{projectId}/attend',
            'App\Http\Controllers\Api\V1_0\ProjectController@attend');

        // Project custom field
        $api->post('projects/{id}/custom_fields',
            'App\Http\Controllers\Api\V1_0\ProjectCustomFieldController@store');
        $api->get('projects/{projectId}/custom_fields',
            'App\Http\Controllers\Api\V1_0\ProjectCustomFieldController@showAll');


        // Project custom field data
        $api->post('projects/{projectId}/members/custom_field_data',
            'App\Http\Controllers\Api\V1_0\ProjectCustomFieldController@fillCustomField');
        $api->post('projects/{projectId}/members/bulk_custom_field_data',
            'App\Http\Controllers\Api\V1_0\ProjectCustomFieldController@fillBulkCustomFields');
        $api->get('projects/{projectId}/members/custom_field_data',
            'App\Http\Controllers\Api\V1_0\ProjectCustomFieldController@showAllCutsomFieldsData');
        $api->get('projects/{projectId}/members/all_custom_field_data',
            'App\Http\Controllers\Api\V1_0\ProjectCustomFieldController@showAllMembersCustomFieldData');
    });

    /*
    |--------------------------------------------------------------------------
    | Refresh token endpoint
    |--------------------------------------------------------------------------
    |
    | Refresh a new JWT
    |
    */
    $api->group(['middleware' => ['jwt.refresh']], function ($api) {
        // For refresh token
        $api->post('auth/refresh_token', function () {
            return response(null, 204); });
        });
});
