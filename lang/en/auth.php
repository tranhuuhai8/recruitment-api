<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'unauthenticated' => 'Authentication is required. Please login.',
    'token_failed' => 'The token is invalid.',
    'token_no_cookie' => 'Your session has expired. Please log in again.',
    'token_expired' => 'The refresh token is invalid or has expired. Please log in again.',
    'account_invalid' => 'Account not found or has been locked.',
    'password_incorrect' => 'Incorrect password.',
    'logout_success' => 'Login out successfully.',
    'login_success' => 'Login in successfully.',
    'login_failed' => 'Your email address or password is incorrect.',
    'register_success' => 'Account registration successful. Please check your gmail to verify your account.',
    'permission_denied' => 'Account does not have access.',
    'register_failed' => 'New registration failed.',
    'email_error' => 'Email does not exist',
    'not_active' => 'Your account is not verified. Please check your email to verify!',
    'locked' => 'Your account has been locked. Please contact the administrator to unlock it!',
    'reset_password_failed' => 'The password reset link has expired. Please request a new one.',
    'verify_success' => 'Your account has been verified successfully. You can now log in.',
    'verify_link_expired' => 'The verification link has expired or is invalid. Please register again.',
    'mail' => [
        'register' => [
            'mail_address' => 'Email',
            'text_verify' => 'Please click here to verify your account and log in!',
        ],
        'forgot_password' => [
            'text' => 'Please open this link to perform the new password update!',
        ],
    ],
];
