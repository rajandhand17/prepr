<?php

namespace App\Notifications;

enum NotificationTypes
{
    const LAB = 'lab';
    const ORGANIZATION = 'organization';
    const CHALLENGE = 'challenge';
    const FRIEND_REQUEST = 'friend_request';
    const MEMBER_INVITATION = [
        self::LAB => 'lab_member_invitation',
        self::CHALLENGE => 'challenge_member_invitation',
        self::ORGANIZATION => 'organization_member_invitation'
    ];
    const LEARNING_POINT = 'learning_point';
}
