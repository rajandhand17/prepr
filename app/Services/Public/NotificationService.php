<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\Notification;
use App\Models\User;
use Exception;

class NotificationService
{
    /**
     * @param $resource
     *
     * @return array|false
     */
    private function prepareMetaData($resource): false|array
    {
        try {
            return [
                'total_count'  => $resource->total(),
                'per_page'     => $resource->perPage(),
                'count'        => $resource->count(),
                'current_page' => $resource->currentPage(),
                'total_pages'  => $resource->lastPage(),
            ];
        } catch (Exception $exception) {
            return false;
        }
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
            $data = Notification::query()
                ->where('notifiable_type', '=', User::class)
                ->where('notifiable_id', '=', $user->id)
                ->whereNotificationType(data_get($filters, 'type'))
                ->latest()
                ->paginate(config('site-settings.notification_pagination_per_page'));
            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'unread_notifications_count' => Notification::query()
                    ->where('notifiable_type', '=', User::class)
                    ->where('notifiable_id', '=', $user->id)
                    ->whereNotificationType(data_get($filters, 'type'))
                    ->where('read_at', '=', null)->count(),
                'list' => $data,
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

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
            $user->notifications()->whereIn('id', $ids)->read();

            return true;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

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
            $user->notifications()->whereIn('id', $ids)->delete();

            return true;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
