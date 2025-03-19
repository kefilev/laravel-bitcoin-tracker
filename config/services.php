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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'bitfinex' => [
        'base_url' => env('BITFINEX_API_URL'),
        'candle_endpoint' => [
            '1' =>  'candles/trade:1h:tBTC{{currency}}/hist?limit=1',
            '6' =>  'candles/trade:6h:tBTC{{currency}}/hist?limit=1',
            '24' => 'candles/trade:1D:tBTC{{currency}}/hist?limit=1',
        ],
        'currencies' => ['USD', 'EUR']
    ]


];
