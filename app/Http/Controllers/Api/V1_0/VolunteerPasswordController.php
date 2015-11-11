<?php

namespace App\Http\Controllers\Api\V1_0;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1_0\CreatePasswordResetRequest;
use App\Http\Requests\Api\V1_0\PasswordResetRequest;
use Password;
use Auth;
use Illuminate\Mail\Message;
use App\Exceptions\InvalidUserException;
use App\Exceptions\GeneralException;

class VolunteerPasswordController extends Controller
{
    public function createPasswordReset(CreatePasswordResetRequest $request)
    {
        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return response(null, 204);
            case Password::INVALID_USER:
                throw new InvalidUserException('The email does not exist', 'inexistence_email');
        }
    }

    public function postPasswordReset($email, $token, PasswordResetRequest $request)
    {
        $credentials = [
            'email' => $email,
            'token' => $token,
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password')
        ];

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
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);

        $user->save();

        Auth::login($user);
    }

    protected function getEmailSubject()
    {
        return 'Password reset';
    }
}
