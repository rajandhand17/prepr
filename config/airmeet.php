<?php

return [
    'airmeet_base_url'               => env('AIRMEET_BASE_URL', ''),
    'airmeet_access_key'             => env('AIRMEET_ACCESS_KEY', ''),
    'airmeet_secret_key'             => env('AIRMEET_SECRET_KEY', ''),
    'airmeet_event_info_url'         => 'prod/airmeet/%s/info',
    'airmeet_add_event_attendee_url' => 'prod/airmeet/%s/attendee',
];
