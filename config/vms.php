<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email verification code expired time
    |--------------------------------------------------------------------------
    |
    | The email verification code expired time in hours.
    | The default value is 8 hours
    |
    */
    'frontendHost' => env('FRONTEND_HOST'),
    'emailVerificationExpired' => env('VMS_EMAIL_VERIFICATION_EXPIRED', 8),
    'avatarRootPath' => env('AVATAR_ROOT_PATH', 'upload/avatars'),
    'avatarHost' => env('AVATAR_HOST'),
    'emailVerificationUrl' => env('FRONTEND_HOST') . env('EMAIL_VERIFICATION_URL'),
    'passwordResetUrl' => env('FRONTEND_HOST') . env('PASSWORD_RESET_URL')
];
