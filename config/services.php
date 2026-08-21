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
        'token' => env('POSTMARK_TOKEN'),
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

    'youtube-trend' => [
        'project' => env('GOOGLE_YOUTUBE_PROJECT'),
        'key' => env('GOOGLE_YOUTUBE_KEY'),
    ],

    // 한알 라이브 생성 (Gemini). config 경유 = config:cache 안전.
    'hanal' => [
        'gemini_key' => env('GEMINI_API_KEY', ''),
        'gen_model' => env('HANAL_GEMINI_GEN_MODEL', 'gemini-3.5-flash'),
        'light_model' => env('HANAL_GEMINI_LIGHT_MODEL', 'gemini-3.1-flash-lite'),
    ],
];
