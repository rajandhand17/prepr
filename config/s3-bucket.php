<?php

return [
    'AWS_ACCESS_KEY_ID'           => env('AWS_ACCESS_KEY_ID'),
    'AWS_SECRET_ACCESS_KEY'       => env('AWS_SECRET_ACCESS_KEY'),
    'AWS_BUCKET'                  => env('AWS_BUCKET'),
    'AWS_DEFAULT_REGION'          => env('AWS_DEFAULT_REGION'),
    'AWS_URL'                     => env('AWS_URL'),
    'AWS_ENDPOINT'                => env('AWS_ENDPOINT'),
    'AWS_USE_PATH_STYLE_ENDPOINT' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
];
