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

    'discussion_module_type' => [
        'lab'       => '0',
        'challenge' => '1',
        'project'   => '2',
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
        'unified'      => '4',
    ],

    'member_management_component_type' => [
        'organization' => '0',
        'lab'          => '1',
        'challenge'    => '2',
        'project'      => '5',
        'lab_program'  => '3',
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
        'organization'   => '0',
        'lab'            => '1',
        'lab_program'    => '2',
        'challenge'      => '3',
        'challenge_path' => '4',
        'project'        => '5',
    ],

    'lab_status' => [
        'draft'   => '0',
        'publish' => '1',
        'archive' => '2',
    ],

    'lab_type' => [
        'assess'  => '0',
        'onboard' => '1',
        'engage'  => '2',
        'grow'    => '3',
        'na'      => '4',
    ],

    'lab_privacy' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'lab_social_activity_is_like' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'lab_social_activity_is_follow' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'lab_social_activity_favourite' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'lab_social_activity_share' => [
        'yes' => '1',
        'no'  => '0',
    ],
    'lab_social_activity_refence_type' => [
        'lab'             => '0',
        'project'         => '1',
        'user'            => '2',
        'challenge'       => '3',
        'challenge-group' => '4',
        'lab-group'       => '5',
    ],

    'lab_component' => [
        'lab'          => 'lab',
        'organization' => 'organization',
    ],

    'resource_module_status' => [
        'draft'   => '0',
        'publish' => '1',
        'archive' => '2',
    ],

    'challenge_status' => [
        'draft'   => '0',
        'publish' => '1',
    ],

    'description_type' => [
        'text'      => '0',
        'scorm'     => '1',
    ],

    'resource_module_type' => [
        'document'             => '0',
        'video'                => '1',
        'audio'                => '2',
        'embedded_video'       => '3',
        'embedded_audio'       => '4',
        'url'                  => '5',
        'image'                => '6',
        'Embedded_Cover_Video' => '7',
    ],

    'resource_module_privacy' => [
        'no'  => '0',
        'yes' => '1',
    ],
    'resource_module_is_global' => [
        'no'  => '0',
        'yes' => '1',
    ],
    'challenge_privacy' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'challenge_notification_enabled' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'challenge_open_close' => [
        'no'  => '1',
        'yes' => '0',
    ],

    'challenge_auto_created' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'challenge_ai_created' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'challenge_assessment_type' => [
        'null'  => '0',
        'open'  => '1',
        'close' => '2',
        'ai'    => '3',
    ],

    'challenge_visibility_type' => [
        'null'   => '0',
        'users'  => '1',
        'hidden' => '2',
    ],

    'challenge_timeline_type' => [
        'flexible'   => '0',
        'restricted' => '1',
    ],

    'challenge_achievement_enable' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'challenge_sequential' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'challenge_requirement_common' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'resource_collection_status' => [
        'draft'   => '0',
        'publish' => '1',
        'archive' => '2',
    ],

    'resource_collection_privacy' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'resource_collection_is_accessible' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'resource_group_status' => [
        'draft'   => '0',
        'publish' => '1',
        'archive' => '2',
    ],

    'resource_group_privacy' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'challenge_announcement_by' => [
        'email' => '0',
        'inbox' => '1',
        'both'  => '2',
    ],

    'challenge_flexible_announcement_by' => [
        'email'        => '0',
        'notification' => '1',
    ],

    'challenge_announcement_send_status' => [
        'send'      => '0',
        'draft'     => '1',
        'scheduled' => '2',
    ],

    'gender' => [
        'male'              => '0',
        'female'            => '1',
        'other'             => '2',
        'decline_to_answer' => '3',
    ],

    'user_achievement_type' => [
        'lab'                 => '0',
        'lab_program'         => '1',
        'challenge'           => '2',
        'challenge_path'      => '3',
        'resource_group'      => '4',
        'appreciation_award'  => '5',
        'activity_award'      => '6',
        'skill_activity'      => '7',
        'imported_award'      => '8',
        'winner_award'        => '9',
        'participation_award' => '10',
    ],

    'recent_immigrant' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'indigenous_group' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'visible_minority' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'disability' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'file_type' => [
        'document'             => '0',
        'video'                => '1',
        'audio'                => '2',
        'embedded'             => '3',
        'embedded_audio'       => '4',
        'url'                  => '5',
        'image'                => '6',
    ],

    'project_view_enabled' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'project_download_enabled' => [
        'no'  => '0',
        'yes' => '1',
    ],

    'project_media_type' => [
        'image'    => '0',
        'embedded' => '1',
        'video'    => '2',
    ],

    'project_privacy' => [
        'public'  => '0',
        'private' => '1',
    ],

    'project_file_type' => [
        'image' => 'image',
        'video' => 'video',
        'docs'  => 'docs',
        'audio' => 'audio',
    ],

    'subscription_options' => [
        '0' => 'unsubscribe',
        '1' => 'monthly',
        '2' => 'weekly',
    ],

    'privacy_options' => [
        '0' => 'public',
        '1' => 'private',
        '2' => 'signed-in',
    ],
    'user_privacy_options' => [
        'public'    => '0',
        'private'   => '1',
        'signed-in' => '2',
    ],
    'friend_request_options' => [
        '0' => 'public',
        '1' => 'private',
    ],

    'notification_options' => [
        'unsubscribe' => '0',
        'monthly'     => '1',
        'weekly'      => '2',
    ],

    'subscribe_unsubscribe' => [
        'unsubscribe' => '0',
        'subscribe'   => '1',
    ],

    'profile_visibility' => [
        'signed-in' => '2',
        'private'   => '1',
        'public'    => '0',
    ],

    'privacy_friend_request' => [
        'public'  => '0',
        'private' => '1',
    ],

    'subscribe_unsubscribe_id' => [
        '0' => 'unsubscribe',
        '1' => 'subscribe',
    ],

    'conversation_type' => [
        'direct_message' => '0',
        'group_message'  => '1',
        'announcement'   => '2',
    ],
    'conversation_type_id' => [
        '0' => 'direct_message',
        '1' => 'group',
        '2' => 'announcement',
    ],

    'purpose' => [
        'looking_teams'                   => '0',
        'currently_mentor'                => '1',
        'looking_employers'               => '2',
        'currently_team'                  => '3',
        'looking_teammates'               => '4',
        'looking_employees'               => '5',
        'looking_invest'                  => '6',
        'looking_mentor'                  => '7',
        'looking_for_investors'           => '8',
        'looking_to_create_social_impact' => '9',
        'looking_to_learn'                => '10',
        'looking_to_solve_problems'       => '11',
        'looking_to_build_skills'         => '12',
    ],

    'user_types' => [
        'employee'              => '0',
        'investor'              => '1',
        'teacher'               => '2',
        'job_seeker'            => '3',
        'student'               => '4',
        'recent_grad'           => '5',
        'expert'                => '6',
        'employer'              => '7',
        'Recent Grad'           => '8',
        'facilitator'           => '9',
        'Job Seeker'            => '10',
        'startup'               => '11',
        'learner'               => '12',
        'mentor'                => '13',
        'innovator'             => '14',
        'aspiring_entrepreneur' => '15',
        'evaluator'             => '16',
        'small'                 => '17',
        'entrepreneur'          => '18',
        'ngo'                   => '19',
        'enterprise'            => '20',
        'applicant'             => '21',
        'educational'           => '22',
        'community'             => '23',
        'educator'              => '24',
        'government'            => '25',
        'others'                => '26',
    ],

    'project_member_management_invite_type' => [
        'email'   => '0',
        'network' => '1',
        'csv'     => '2',
    ],

    'project_member_management_invite_status' => [
        'invited'  => '0',
        'accepted' => '1',
        'pending'  => '2',
        'declined' => '3',
    ],

    'project_member_management_email_status' => [
        'scheduled' => '0',
        'sent'      => '1',
        'fail'      => '2',
        'na'        => '3',
    ],

    'project_access_level' => [
        'viewer'      => '0',
        'editor'      => '1',
        'team_leader' => '2',
    ],

    'recent_immigration' => [
        'yes' => '1',
        'no'  => '2',
    ],

    'module_component_type' => [
        'organization' => '0',
        'lab'          => '1',
        'challenge'    => '2',
        'project'      => '3',
    ],
    'campus_connect_status' => [
        'job'   => '0',
        'story' => '1',
        'both'  => '2',
        'no'    => '3',
    ],
    'campus_connect_status_id' => [
        '0' => 'job',
        '1' => 'story',
        '2' => 'both',
        '3' => 'no',
    ],
    'notification_permission' => [
        'yes' => '0',
        'no'  => '1',
    ],
    'use_main_org_logo' => [
        'no'    => '0',
        'yes'   => '1',
    ],
    'sso_type' => [
        'google'    => '1',
        'linkedin'  => '2',
        'microsoft' => '3',
        'apple'     => '4',
        'magnet'    => '5',
    ],

    'visit_type_id' => [
        'document'              => '0',
        'video'                 => '1',
        'audio'                 => '2',
        'embedded'              => '3',
        'embedded_audio'        => '4',
        'url'                   => '5',
        'image'                 => '6',
        'scorm'                 => '7',
        'go1'                   => '8',
    ],

    'module_type' => [
        'labs'                    => '0',
        'lab_programs'            => '1',
        'challenges'              => '2',
        'challenge_paths'         => '3',
        'resource_modules'        => '4',
        'resource_collections'    => '5',
        'resource_group'          => '6',
        'projects'                => '7',
    ],

    'assessment_type' => [
        'no_evaluation'         => 'noEvAttachments',
        'open_evaluation'       => 'openEvAttachments',
        'close_evaluation'      => 'closeEvAttachments',
    ],

    'resource_types' => [
        'assess'  => '0',
        'onboard' => '1',
        'engage'  => '2',
        'grow'    => '3',
    ],

    'resource_types_key' => [
        '0'       => 'assess',
        '1'       => 'onboard',
        '2'       => 'engage',
        '3'       => 'grow',
    ],
    'resource_mode_type' => [
        'team'       => '4',
        'individual' => '5',
    ],
    'resource_mode_type_key' => [
        '4'       => 'team',
        '5'       => 'individual',
    ],
    'resource_mode'=> [
        'type' => '0',
        'mode' => '1',
    ],

    'module_completion_statuses_types'=> [
        'resource_module'     => '4',
        'resource_collection' => '5',
        'resource_group'      => '6',
    ],

    'status_module_completion'=> [
        'not_started' => '0',
        'in_progress' => '1',
        'completed'   => '2',
    ],

    'resource_media_type' => [
        'image'    => '0',
        'embedded' => '1',
        'video'    => '2',
    ],
    'lab_mode_type'=> [
        'type' => '0',
        'mode' => '1',
    ],
    'lab_modes' => [
        'team'       => '4',
        'individual' => '5',
    ],
    'lab_program_mode_type'=> [
        'type' => '0',
        'mode' => '1',
    ],
    'lab_program_modes' => [
        'team'       => '4',
        'individual' => '5',
    ],
    'lab_program_type' => [
        'assess'  => '0',
        'onboard' => '1',
        'engage'  => '2',
        'grow'    => '3',
        'na'      => '4',
    ],
    'dashboard_type' => [
        'user'          => '0',
        'lab'           => '1',
        'organization'  => '2',
    ],
    'dashboard_card_type' => [
        'reports'           => '0',
        'deadlines'         => '1',
        'leaderboard'       => '2',
        'my-challenges'     => '3',
        'my-labs'           => '4',
        'my-projects'       => '5',
        'my-resources'      => '6',
        'my-organizations'  => '7',
        'subscription'      => '8',
        'inbox-friends'     => '9',
        'recommendations'   => '10',
        'continue-left'     => '11',
        'achievement'       => '12',
    ],
    'member_management_component' => [
        'lab_program'       => 'lab-program',
    ],
];
