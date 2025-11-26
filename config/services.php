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

    'mock_api' => [
        'headers' => [
            'name' => 'x-mock-response-name',
            'value' => [
                'sucesso_pix' => 'SUCESSO_PIX',
                'erro_pix' => 'ERRO_PIX',
                'sucesso_wd' => 'SUCESSO_WD',
                'erro_wd' => 'ERRO_WD',
            ]
        ]
    ],
    'subadquirentes' => [
        'subadq_a' => [
            'base_url' => env('SUBADQ_A_BASE_URL', 'https://api.subadq_a.com'),
        ],
        'subadq_b' => [
            'base_url' => env('SUBADQ_B_BASE_URL', 'https://api.subadq_b.com'),
        ],
    ],

];
