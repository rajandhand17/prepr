<?php

return [
    'client_id'             => env('GO1_CLIENT_ID', 'null'),
    'client_secret'         => env('GO1_CLIENT_SECRET', 'null'),
    'default_user_password' => env('GO1_DEFAULT_USER_PASSWORD', 'FtC7gZ837T'),
    'prepr_id'              => env('PREPR_ORG_ID', 19),
    'email_prefix'          => env('GO1_EMAIL_PREFIX', 'prepr_prod_integration'),
    'total_resource_data'   => env('GO1_TOTAL_RESOURCE_DATA', 10000),
    'per_page'              => env('GO1_RESOURCE_PER_PAGE', 9),
    'base_url'              => env('GO1_BASE_URL'),
    'auth_url'              => env('GO1_AUTH_URL'),
    'api_version'           => env('GO1_API_VERSION'),
];
