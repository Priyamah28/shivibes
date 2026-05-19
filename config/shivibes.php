<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shivibes mail settings
    |--------------------------------------------------------------------------
    */

    'mail' => [
        'use_queue' => env('MAIL_USE_QUEUE', false),

        'from_name' => env('MAIL_FROM_NAME', 'Shivibes'),

        'noreply_address' => env('MAIL_FROM_ADDRESS', 'noreply@shivibes.com'),

        'support_address' => env('MAIL_SUPPORT_ADDRESS', 'support@shivibes.com'),

        'admin_emails' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('ADMIN_NOTIFICATION_EMAILS', 'support@shivibes.com'))
        ))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email OTP verification
    |--------------------------------------------------------------------------
    */

    'otp' => [
        'length' => (int) env('OTP_LENGTH', 6),
        'expires_minutes' => (int) env('OTP_EXPIRES_MINUTES', 5),
        'max_verify_attempts' => (int) env('OTP_MAX_VERIFY_ATTEMPTS', 5),
        'resend_cooldown_seconds' => (int) env('OTP_RESEND_COOLDOWN_SECONDS', 60),
        'max_resends_per_hour' => (int) env('OTP_MAX_RESENDS_PER_HOUR', 5),
    ],

];
