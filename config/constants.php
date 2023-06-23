<?php

return [
    'role_name' => [
        'organization_owner'   => 'Organization Owner',
        'organization_manager' => 'Organization Manager',
        'lab_manager'          => 'Lab Manager',
        'challenge_manager'    => 'Challenge Manager',
        'resource_manager'     => 'Resource Manager',
        'user'                 => 'User',

    ],
    'role_type' => [
        'internal' => '0',
        'external' => '1',
    ],

    'member_management_type' => [
        'invite'       => '0',
        'join_request' => '1',
        'auto_created' => '2',
    ],

    'member_management_invite_type' => [
        'email'        => '0',
        'network'      => '1',
        'join_request' => '2',
        'csv'          => '3',
    ],

    'member_management_component_type' => [
        'organization' => '0',
        'lab'          => '1',
        'challenge'    => '2',
        'project'      => '3',
    ],

    'member_management_invite_status' => [
        'invited'      => '0',
        'accepted'     => '1',
        'pending'      => '2',
        'declined'     => '3',
        'auto_created' => '4',
    ],

    'member_management_auto_invite' => [
        'no'  => '0',
        'yes' => '1',
        'na'  => '2',
    ],

    'member_management_email_status' => [
        'scheduled' => '0',
        'sent'      => '1',
        'fail'      => '2',
    ],
];
