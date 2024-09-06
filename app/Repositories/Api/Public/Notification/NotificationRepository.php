<?php

namespace App\Repositories\Api\Public\Notification;

use App\Models\User;
use App\Services\Public\NotificationService;
use Exception;

class NotificationRepository implements NotificationInterface
{
    /**
     * @param NotificationService $notificationService
     */
    public function __construct(protected NotificationService $notificationService)
    {
    }

    /**
     * @param User  $user
     * @param array $filters
     *
     * @return false|array
     */
    public function fetchNotification(User $user, array $filters): false|array
    {
        try {
            return $this->notificationService->fetchNotification($user, $filters);
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param User  $user
     * @param array $ids
     *
     * @return bool
     */
    public function markAsRead(User $user, array $ids): bool
    {
        try {
            return $this->notificationService->markAsRead($user, $ids);
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param User  $user
     * @param array $ids
     *
     * @return bool
     */
    public function delete(User $user, array $ids): bool
    {
        try {
            return $this->notificationService->delete($user, $ids);
        } catch (Exception $exception) {
            return false;
        }
    }
}
