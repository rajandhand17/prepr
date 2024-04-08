<?php

return [
    'client_id' => env('GO1_CLIENT_ID', 'null'),
    'client_secret' => env('GO1_CLIENT_SECRET', 'null'),
    "default_user_password" => env("GO1_DEFAULT_USER_PASSWORD", 'FtC7gZ837T'),
    "prepr_id" => env("PREPR_ORG_ID", 19),
    "email_prefix" => env("GO1_EMAIL_PREFIX", 'prepr_prod_integration'),
];
