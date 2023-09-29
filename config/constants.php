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
        'na'        => '3',
    ],

    'email_template_type' => [
        'invitation' => '0',
    ],

    'email_template_module_type' => [
        'organization'      => '0',
        'lab'               => '1',
        'lab_program'       => '2',
        'challenge'         => '3',
        'challenge_path'    => '4',
        'project'           => '5',
    ],

    'lab_status'=> [
        'draft'   => '0',
        'publish' => '1',
        'archive' => '2',
    ],

    'lab_type'=> [
        'assess'   => '0',
        'onboard'  => '1',
        'engage'   => '2',
        'grow'     => '3',
        'na'       => '4',
    ],

    'lab_privacy'=> [
        'no'  => '0',
        'yes' => '1',
    ],

    'lab_social_activity_is_like'=> [
        'yes'    => '1',
        'no'     => '2',
    ],

    'lab_social_activity_is_follow'=> [
        'yes'    => '1',
        'no'     => '2',
    ],

    'lab_social_activity_favourite'=> [
        'yes'    => '1',
        'no'     => '2',
    ],

    'lab_social_activity_share'=> [
        'yes'    => '1',
        'no'     => '0',
    ],
    'lab_social_activity_refence_type'=> [
        'lab'            => '0',
        'project'        => '1',
        'user'           => '2',
        'challenge'      => '3',
        'challenge-group'=> '4',
        'lab-group'      => '5',
    ],

    'lab_component'=> [
        'lab'            => 'lab',
        'organization'   => 'organization',
    ],

    'resource_module_status'=> [
        'draft'   => '0',
        'publish' => '1',
        'archive' => '2',
    ],

    'resource_module_type'=> [
        'document'             => '0',
        'video'                => '1',
        'audio'                => '2',
        'embedded_video'       => '3',
        'embedded_audio'       => '4',
        'url'                  => '5',
        'image'                => '6',
        'Embedded_Cover_Video' => '7',
    ],

    'resource_module_privacy'=> [
        'no'  => '0',
        'yes' => '1',
    ],
    'resource_module_is_global'=> [
        'no'  => '0',
        'yes' => '1',
    ],
];
