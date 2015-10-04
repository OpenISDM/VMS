<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Requests\Api\V1_0\VolunteerRegistrationRequest;
use App\Http\Requests\Api\V1_0\CredentialRequest;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use App\Volunteer;
use App\City;
use App\VerificationCode;
use App\Jobs\SendVerificationEmail;
use App\Utils\StringUtil;
use App\Http\Responses\Error;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class VolunteerAuthController extends Controller
{
    use Helpers;

    public function __construct()
    {
        if (env('APP_ENV') == 'testing' && array_key_exists("HTTP_AUTHORIZATION", request()->server())) {
            JWTAuth::setRequest(\Route::getCurrentRequest());
        } 
    }

    /**
     * Register a new volunteer. The request will be validated by 
     * App\Http\Middleware\CheckHeaderFieldsMiddleware and 
     * App\Http\Requests\Api\V1_0\VolunteerRegistrationRequest classes
     * 
     * 
     * @param  VolunteerRegistrationRequest $request
     * @return Response                             
     */
    public function register(VolunteerRegistrationRequest $request)
    {
        // Get volunteer data, except city object
        $volunteerInput = $request->except(['city', 'password']);
        // Get city ID
        $cityId = $request->input('city.id');
        $verificationCodeString = StringUtil::generateHashToken();
        
        // Create a new volunteer
        $volunteer = Volunteer::firstOrNew($volunteerInput);
        $city = City::find($cityId);
        
        $volunteer->password = bcrypt($request->password); 
        $volunteer->city()->associate($city);
        $volunteer->save();
        
        // Save verification code into the volunteer
        $verificationCode = new VerificationCode(['code' => $verificationCodeString]);
        $verificationCode->volunteer()->associate($volunteer);
        $verificationCode->save();

        // Send verification email to an queue
        $this->dispatch(new SendVerificationEmail($volunteer, $verificationCodeString, 'Verification email'));

        // Generate JWT (JSON Web Token)
        $credentials = $request->only('username', 'password');

        try {
            // Authenticate
            if (! $token = JWTAuth::attempt($credentials)) {
                $message = 'Authentication failed';
                $error = new Error('incorrect_login_credentials');
                $statusCode = 401;

                return response()->apiJsonError($message, $error, $statusCode);
            }
        } catch (JWTException $e) {
            $message = 'Server error';
            $error = new Error('could_not_create_token');
            $statusCode = 500;

            // TODO: Log error issue

            return response()->apiJsonError($message, $error, $statusCode);
        }
        
        $rootUrl = request()->root();

        $responseJson = [
            'href' => env('APP_URL', $rootUrl) . '/api/users/me',
            'username' => $volunteer->username, 
            'auth_access_token' => $token
        ];

        return response()->json($responseJson, 201);
    }

    public function login(CredentialRequest $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                $message = 'Authentication failed';
                $error = new Error('incorrect_login_credentials');
                $statusCode = 401;

                return response()->apiJsonError($message, $error, $statusCode);
            }
        } catch (JWTException $e) {
            $message = 'Server error';
            $error = new Error('could_not_create_token');
            $statusCode = 500;

            return response()->apiJsonError($message, $error, $statusCode);
        }

        // Check if the volunteer was locked
        $volunteer = Volunteer::where('username', '=', $credentials['username'])->first();

        if ($volunteer->is_locked == 1 || $volunteer->is_locked == true) {
            $token = null;

            $message = 'Authentication failed';
            $error = new Error('account_was_locked');
            $statusCode = 401;

            return response()->apiJsonError($message, $error, $statusCode);
        }

        $rootUrl = request()->root();

        $responseJson = [
            'href' => env('APP_URL', $rootUrl) . '/api/users/me', 
            'auth_access_token' => $token
        ];

        return response()->json($responseJson, 200);
    }

    /**
     * Verify volunteer's email address with verification code.
     * It will check the volunteer's verification code and the expired time
     * 
     * @param  EmailVerificationRequest $reuqest 
     * @return Response
     */
    public function emailVerification($emailAddress, $verificationCode)
    {
        // Get now time
        $nowDateTime = new \DateTime();

        // Get authenticated volunteer
        if (! $volunteerAuth = JWTAuth::parseToken()->authenticate()) {
            $message = 'Not Found';
            $error = new Error('volunteer_not_found');
            $statusCode = 404;

            return response()->apiJsonError($message, $error, $statusCode);
        }

        // Check email address
        if (strcmp($emailAddress, $volunteerAuth->email) !== 0) {
            $message = 'Not Found';
            $error = new Error('volunteer_not_found');
            $statusCode = 404;

            return response()->apiJsonError($message, $error, $statusCode);
        }

        // Check verification code
        $volunteer = Volunteer::find($volunteerAuth->id);
        $code = $volunteer->verificationCode->code;

        if (strcmp($verificationCode, $code) !== 0) {
            $message = 'Unvalidated or expired verification token';
            $error = new Error('unvalidated_expired_verification_token');
            $statusCode = 404;

            return response()->apiJsonError($message, $error, $statusCode);
        }
        
        /**
         * Check if verification is expired
         */
        // Get expired time in configuration file
        $expiredTime = config('vms.emailVerificationExpired', 8);   // hours
        $codeCreatedTime = $volunteer->verificationCode->created_at;

        $codeCreatedDateTime = new \DateTime($codeCreatedTime);
        $interval = new \DateInterval('PT' . $expiredTime . 'H');

        $expiredDateTime = $codeCreatedTime->add($interval);

        if ($nowDateTime > $expiredDateTime) {
            $message = 'Unvalidated or expired verification token';
            $error = new Error('unvalidated_expired_verification_token');
            $statusCode = 404;

            return response()->apiJsonError($message, $error, $statusCode);
        }

        // Delete the verification code
        $volunteer->verificationCode->delete();
        
        // Set the volunteer into active
        $volunteer->is_actived = true;
        $volunteer->save();

        $responseJson = [
            'message' => 'Successful email verification'
        ];

        return response()->json($responseJson, 200);
    }
}