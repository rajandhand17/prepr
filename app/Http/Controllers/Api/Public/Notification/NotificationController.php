<?php

namespace App\Http\Controllers\Api\Public\Notification;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Public\Notification\NotificationMarkAsRequest;
use App\Http\Requests\Public\Notification\NotificationRequest;
use App\Http\Resources\Public\Notification\NotificationResource;
use App\Models\User;
use App\Repositories\Api\Public\Notification\NotificationRepository;

class NotificationController extends AppBaseController
{
    /**
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(protected NotificationRepository $notificationRepository)
    {
    }

    public function index(NotificationRequest $request)
    {
        try {
            /** @var User $user */
            $user = auth()->user();
            $data = $this->notificationRepository->fetchNotification($user, ['type' => $request->get('type')]);
            if ($data === false) {
                return $this->sendError(__('responses.failed_to_fetch_notification'));
            }

            return $this->sendResponse([
                ...$data,
                'list' => NotificationResource::collection(data_get($data, 'list')),
            ], __('responses.notifications_list'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.failed_to_fetch_notifications'));
        }
    }

    public function markAsRead(NotificationMarkAsRequest $request)
    {
        try {
            /** @var User $user */
            $user = auth()->user();
            $data = $this->notificationRepository->markAsRead($user, $request->get('notification_ids', []));
            if ($data === false) {
                return $this->sendError(__('responses.failed_to_mark_as_read'));
            }

            return $this->sendResponse(null, __('responses.notifications_marked_as_read'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.failed_to_mark_as_read'));
        }
    }

    public function deleteNotification(string $notificationId)
    {
        try {
            /** @var User $user */
            $user = auth()->user();
            $data = $this->notificationRepository->delete($user, [$notificationId]);
            if ($data === false) {
                return $this->sendError(__('responses.failed_to_delete_notification'));
            }

            return $this->sendResponse(null, __('responses.notification_deleted'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.failed_to_delete_notification'));
        }
    }
}
