<?php

namespace App\Repositories\Api\Chat\Conversation;

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
            return false;
        }
    }

    public function list(string $type)
    {
        try {
            return $this->conversationService->list($type);
        } catch (Exception $e) {
            return false;
        }
    }


    public function archiveOrSeenOrDelete(string $uuid, string $action)
    {
        try {
            return $this->conversationService->archiveOrSeenOrDelete($uuid, $action);
        } catch (Exception $e) {
            return false;
        }
    }
}
