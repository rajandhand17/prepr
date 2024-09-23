<?php

namespace App\Models\Builder;

use App\Notifications\NotificationTypes;
use Illuminate\Database\Eloquent\Builder;

class NotificationBuilder extends Builder
{
    public function whereNotificationType(string|null $type): NotificationBuilder
    {
        if (!$type) {
            return $this->whereIn('type', [
                NotificationTypes::LAB,
                NotificationTypes::ORGANIZATION,
                NotificationTypes::CHALLENGE,
                NotificationTypes::FRIEND_REQUEST,
                NotificationTypes::LEARNING_POINT,
                NotificationTypes::COMMENT,
            ]);
        }

        return $this->where('type', '=', $type);
    }
}
