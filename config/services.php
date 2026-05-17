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

    'pdftotext_path' => env('PDFTOTEXT_PATH', 'pdftotext'),
    'pdf_remote_extract_max_bytes' => (int) env('PDF_REMOTE_EXTRACT_MAX_BYTES', 31457280),

    'project_uploads' => [
        'direct_driver' => env('PROJECT_DIRECT_UPLOAD_DRIVER'),
        'blob_handle_url' => env('PROJECT_BLOB_HANDLE_URL', '/blob/project-upload'),
        'blob_access' => env('PROJECT_BLOB_ACCESS', 'public'),
        'max_bytes' => (int) env('PROJECT_UPLOAD_MAX_BYTES', 104857600),
        'secret' => env('BLOB_UPLOAD_SECRET', env('APP_KEY')),
    ],

];
