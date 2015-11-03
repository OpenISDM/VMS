<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Requests\Api\V1_0\VolunteerRegistrationRequest;
use App\Http\Requests\Api\V1_0\CredentialRequest;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Services\AvatarStorageService;
use Dingo\Api\Routing\Helpers;
use App\Volunteer;
use App\City;
use App\VerificationCode;
use App\Jobs\SendVerificationEmail;
use App\Utils\StringUtil;
use App\Http\Responses\Error;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\VolunteerRepository;
use App\Repositories\CityRepository;
use App\Repositories\VerificationCodeRepository;
use App\Services\JwtService;
use App\Services\VerifyEmailService;
use App\Commands\VerifyEmailCommand;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\AuthenticatedUserNotFoundException;
use App\Exceptions\NotFoundException;

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
     * @param  VolunteerRegistrationRequest $request
     * @return Response                             
     */
    public function register(VolunteerRegistrationRequest $request)
    {
        // Get volunteer data, except city object
        $volunteerInput = $request->except(['city', 'avatar']);
        // Get city ID
        $cityId = $request->input('city.id');

        // Save avatar name
        if ($request->has('avatar')) {
            $avatarBase64File = $request->input('avatar');
            $avatarStorageService = new AvatarStorageService();

            $avatarStorageService->save($avatarBase64File);
            $volunteerInput['avatar_path'] = $avatarStorageService->getFileName();
        }

        // Find city entity
        $cityRepository = new CityRepository();
        $volunteerInput['city'] = $cityRepository->findById($cityId);

        // Create a volunteer entity
        $volunteerRepository = new VolunteerRepository();
        $volunteer = $volunteerRepository->create($volunteerInput);
                
        // Save verification code
        $verificationCodeString = StringUtil::generateHashToken();
        $verificationCodeRepository = new VerificationCodeRepository();
        $verificationCodeRepository->create(['code' => $verificationCodeString], $volunteer);

        // Send verification email to an queue
        $this->dispatch(new SendVerificationEmail($volunteer, $verificationCodeString, 'VMS 電子郵件驗證'));
        
        $credentials = $request->only('username', 'password');

        // Generate JWT (JSON Web Token)
        $jwtSerivce = new JwtService();
        $token = $jwtSerivce->getToken($credentials);
        
        $rootUrl = request()->root();
        $responseJson = [
            'href' => env('APP_URL', $rootUrl) . '/api/users/me',
            'username' => $volunteer->username,
            'auth_access_token' => $token
        ];

        return response()->json($responseJson, 201);
    }

    /**
     * Volunteer logs in the system. 
     * It will response the JSON Web token.
     * 
     * @param  CredentialRequest $request
     * @return Response
     */
    public function login(CredentialRequest $request)
    {
        $credentials = $request->only('username', 'password');

        // Generate JWT (JSON Web Token)
        $jwtSerivce = new JwtService();
        $token = $jwtSerivce->getToken($credentials);

        // Check if the volunteer was locked
        $volunteer = Volunteer::where('username', '=', $credentials['username'])->first();

        if ($volunteer->is_locked == 1 || $volunteer->is_locked == true) {
            $token = null;
            $message = 'Authentication failed';
            $error = new Error('account_was_locked');

            throw new UnauthorizedException($message, $error);
        }

        $rootUrl = request()->root();

        $responseJson = [
            'href' => env('APP_URL', $rootUrl) . '/api/users/me',
            'auth_access_token' => $token
        ];

        return response()->json($responseJson, 200);
    }

    /**
     * Volunteer logs out the system.
     * The JWT token will be in blacklist.
     * 
     * @return Response
     */
    public function logout()
    {
        if (! $token = JWTAuth::getToken()) {
            $message = 'Failed to logout';
            $error = new Error('no_existing_auth_access_token');
            $statusCode = 400;

            return response()->apiJsonError($message, $error, $statusCode);
        }

        JWTAuth::invalidate($token);

        return response(null, 204);
    }

    /**
     * Verify volunteer's email address with verification code.
     * It will check the volunteer's verification code and the expired time
     * @param  EmailVerificationRequest $reuqest 
     * @return Response
     */
    public function emailVerification($emailAddress, $verificationCode)
    {
        $jwtSerivce = new JwtService();
        $volunteer = $jwtSerivce->getVolunteer();

        $service = new VerifyEmailService($volunteer, $emailAddress, $verificationCode);
        $command = new VerifyEmailCommand($service);
        $command->execute();

        $responseJson = [
            'message' => 'Successful email verification'
        ];

        return response()->json($responseJson, 200);
    }

    /**
     * Resend a new email verification
     * @return [type] [description]
     */
    public function resendEmailVerification()
    {
        $jwtSerivce = new JwtService();
        $volunteer = $jwtSerivce->getVolunteer();

        $volunteer->verificationCode()->delete();

        $verificationCodeString = StringUtil::generateHashToken();
        // Save verification code into the volunteer
        $verificationCodeRepository = new VerificationCodeRepository();
        $verificationCodeRepository->create(['code' => $verificationCodeString], $volunteer);

        // Send verification email to an queue
        $this->dispatch(new SendVerificationEmail($volunteer, $verificationCodeString, 'VMS 電子郵件驗證'));

        return response(null, 204);
    }
}
