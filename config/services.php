<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'marzepay' => [
        'base_url' => env('MARZEPAY_BASE_URL', 'https://wallet.wearemarz.com/api/v1'),
        'api_key' => env('MARZEPAY_API_KEY'),
        'api_secret' => env('MARZEPAY_API_SECRET'),
        'webhook_secret' => env('MARZEPAY_WEBHOOK_SECRET'),
        'country' => env('MARZEPAY_COUNTRY', 'UG'),
        'callback_path' => env('MARZEPAY_CALLBACK_PATH', 'payments/marzepay/webhook'),
        'refund_path' => env('MARZEPAY_REFUND_PATH', 'refund-money'),
        'timeout' => (int) env('MARZEPAY_TIMEOUT', 30),
    ],

];
