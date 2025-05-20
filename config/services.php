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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'gcaptcha' => [
        'key' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'gmaps' => [
        'key' => env('GMAPS_API_KEY'),
    ],
    'mailjet' => [
        'key' => env('MAILJET_KEY'),
        'secret' => env('MAILJET_SECRET'),
    ],
    'paypal' => [
        'apiUrl' => env('PAYPAL_API_URL'),
        'clientID' => env('PAYPAL_CLIENT_ID'),
        'secretKey' => env('PAYPAL_CLIENT_SECRET'),
    ],
    'stripe' => [
        'apiUrl' => env('STRIPE_API_URL'),
        'clientIDSecondary' => env('STRIPE_CLIENT_ACCOUNT_SECONDARY'),
        'secretKeySecondary' => env('STRIPE_SECRET_ACCOUNT_SECONDARY'),
        'clientIDPrimary' => env('STRIPE_CLIENT_ACCOUNT_PRIMARY'),
        'secretKeyPrimary' => env('STRIPE_SECRET_ACCOUNT_PRIMARY'),        
    ],    
    'digital_ocean' => [
        'key' => env('DO_SPACES_KEY'),
        'secret' => env('DO_SPACES_SECRET'),
        'bucket' => env('DO_SPACES_BUCKET'),
    ],
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'phone' => env('TWILIO_WHATSAPP_NUMBER'),
    ],    
];
