<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Exceptions\GeneralException;
use App\Exceptions\InvalidUserException;
use App\Http\Controllers\Api\BaseAuthController;
use App\Http\Requests\Api\V1_0\ChangePasswordRequest;
use App\Http\Requests\Api\V1_0\ForgotPasswordRequest;
use App\Http\Requests\Api\V1_0\PasswordResetRequest;
use App\Http\Requests\Api\V1_0\VerifyPasswordResetRequest;
use App\Services\JwtService;
use App\Volunteer;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Password;

class VolunteerPasswordController extends BaseAuthController
{
    use ResetsPasswords;

    /**
     * Create a password reset request.
     *
     * @param App\Http\Requests\Api\V1_0\ForgotPasswordRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink(
            $this->getSendResetLinkEmailCredentials($request),
            $this->resetEmailBuilder()
        );

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return response(null, 204);
            case Password::INVALID_USER:
                throw new InvalidUserException('The email does not exist', 'nonexistent_email');
        }
    }

    public function verifiyPasswordReset(VerifyPasswordResetRequest $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');

        $user = Volunteer::where('email', $email)->first();

        $check = Password::tokenExists($user, $token);

        if (!$check) {
            throw new GeneralException('Resetting password verfification failure', 'password_reset_verification_failure', 403);
        }

        return $this->response->noContent();
    }

    /**
     * Reset password
     * It will check the token. If the token is correct,
     * volunteer is able to reset his/her password.
     *
     * @param string                                          $email
     * @param string                                          $token
     * @param App\Http\Requests\Api\V1_0\PasswordResetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postPasswordReset(PasswordResetRequest $request)
    {
        $credentials = $request->only([
            'email',
            'token',
            'password',
            'password_confirmation',
        ]);

        $response = Password::reset($credentials, function ($volunteer, $password) {
            $this->resetPassword($volunteer, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return response(null, 204);
            default:
                throw new GeneralException('Unable to reset password', 'cannot_reset_password', 400);
        }
    }

    /**
     * Volunteer changes his/her own password
     * It will validate the new_password by ChangePasswordRequest.
     *
     * @param App\Http\Requests\Api\V1_0\ChangePasswordRequest $request
     * @param App\Services\JwtService                          $jwtService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postChangePassword(ChangePasswordRequest $request, JwtService $jwtService)
    {
        $volunteer = $jwtService->getVolunteer();
        $credentials = [
            'username' => $volunteer->username,
            'password' => $request->input('existing_password'),
        ];

        // Check credentials
        $jwtService->getToken($credentials);
        $newPassword = $request->input('new_password');

        $volunteer->password = bcrypt($newPassword);
        $volunteer->save();

        return response(null, 204);
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string                                      $password
     *
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);

        $user->save();

        Auth::login($user);
    }

    /**
     * Get the subject of password reset email.
     *
     * @return string
     */
    protected function getPasswordResetEmailSubject()
    {
        return 'Password reset';
    }
}
