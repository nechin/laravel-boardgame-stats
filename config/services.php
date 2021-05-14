<?php

use App\Services\BGG\Api2\BggApi2;

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bgg' => [
        'provider' => BggApi2::class,
        'api_path' => 'https://www.boardgamegeek.com/xmlapi2/',
        'plays_count' => env('BGG_PLAYS_COUNT', 2000),
        'cache_enabled' => env('BGG_CACHE_ENABLED', true),
        'cache_seconds' => env('BGG_CACHE_SECONDS', 3600),
    ],

];
