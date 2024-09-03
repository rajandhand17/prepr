<?php

return [
    /*
    |--------------------------------------------------------------------------
    | UNIFIED USE FAKER
    |--------------------------------------------------------------------------
    |
    | USED WHILE TESTING
    |
    */
    'use_faker' => env('UNIFIED_USE_FAKER'),

    /*
    |--------------------------------------------------------------------------
    | UNIFIED WORKSPACE ID
    |--------------------------------------------------------------------------
    |
    | Unified workspace id used
    |
    */
    'workspace' => env('UNIFIED_WORKSPACE'),

    /*
    |--------------------------------------------------------------------------
    | UNIFIED KEY
    |--------------------------------------------------------------------------
    |
    | Unified key
    |
    */
    'key' => env('UNIFIED_KEY'),

    /*
    |--------------------------------------------------------------------------
    | UNIFIED BASE URL
    |--------------------------------------------------------------------------
    |
    | Unified base api url
    |
    */
    'base_url' => env('UNIFIED_BASE_URL', 'https://api.unified.to'),

    /*
    |--------------------------------------------------------------------------
    | UNIFIED APP ENV
    |--------------------------------------------------------------------------
    |
    | For identifying env unified.
    |
    */
    'env' => 'PRODUCTION',

    /*
    |--------------------------------------------------------------------------
    | UNIFIED USAGE TYPES
    |--------------------------------------------------------------------------
    |
    | Unified is being used for the following cases
    |
    */
    'usage_types' => [
        'organization_member_invite' => '0', // INVITING MEMBER INTO THE ORGANIZATION
        'lab_member_invite'          => '1', // INVITING MEMBER TO THE LAB
        'challenge_member_invite'    => '2', // INVITING MEMBER TO THE CHALLENGE
    ],

    /*
    |--------------------------------------------------------------------------
    | UNIFIED URL END POINTS
    |--------------------------------------------------------------------------
    |
    | Unified used api endpoints
    |
    */
    'url_paths' => [
        'integration'       => '/unified/integration/workspace/%s',
        'employee_list'     => '/hris/%s/employee',
        'unified_login_url' => '%s/unified/integration/auth/%s/%s?%s',
    ],

    'pagination_per_page' => 200,
];
