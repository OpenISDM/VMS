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
    'emailVerificationExpired' => env('VMS_EMAIL_VERIFICATION_EXPIRED', 8),
    'avatarRootPath' => env('AVATAR_ROOT_PATH', 'upload/avatars'),
];
