Click here to reset your password: <a href="{{ $link = config('vms.passwordResetUrl') . '?token=' . $token . '&email=' . urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
