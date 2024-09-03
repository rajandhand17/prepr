<?php

namespace App\Repositories\Api\Public\Notification;

use App\Models\User;

interface NotificationInterface
{
    public function fetchNotification(User $user, array $filters): false|array;
    public function markAsRead(User $user, array $ids): bool;

    public function delete(User $user, array $ids): bool;
}
