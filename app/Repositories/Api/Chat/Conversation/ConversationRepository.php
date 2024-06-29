<?php

namespace App\Repositories\Api\Chat\Conversation;

use App\Helpers\UtilityHelper;
use App\Services\Chat\ConversationService;
use Exception;

class ConversationRepository implements ConversationInterface
{
    public function __construct(private ConversationService $conversationService)
    {
    }

    /**
     * @throws Exception
     */
    public function create(array $data)
    {
        try {
            return $this->conversationService->create($data);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getByUUID(string $uuid)
    {
        try {
            $conversation = $this->conversationService->getByUUID($uuid);
            if (!$conversation) {
                return false;
            }

            return $conversation;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function list(string $type)
    {
        try {
            return $this->conversationService->list($type);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function archiveOrUnarchiveOrSeenOrDelete(string $uuid, string $action)
    {
        try {
            return $this->conversationService->archiveOrUnarchiveOrSeenOrDelete($uuid, $action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function onlineOrOffline($id, $action)
    {
        try {
            return $this->conversationService->onlineOrOffline($id, $action);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
