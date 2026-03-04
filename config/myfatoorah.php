<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MyFatoorah Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for MyFatoorah payment gateway integration.
    | Get your API key from: https://myfatoorah.readme.io/docs/test-token
    |
    */

    'api_key' => env('MYFATOORAH_API_KEY', ''),

    'test_mode' => env('MYFATOORAH_TEST_MODE', true),

    'country_iso' => env('MYFATOORAH_COUNTRY_ISO', 'QAT'),

    'webhook_secret_key' => env('MYFATOORAH_WEBHOOK_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Supported Countries
    |--------------------------------------------------------------------------
    |
    | ISO country codes supported by MyFatoorah
    |
    */
    'supported_countries' => [
        'QAT' => 'Qatar',
        'KWT' => 'Kuwait',
        'SAU' => 'Saudi Arabia',
        'ARE' => 'United Arab Emirates',
        'BHR' => 'Bahrain',
        'OMN' => 'Oman',
        'JOD' => 'Jordan',
        'EGY' => 'Egypt',
    ],
];

