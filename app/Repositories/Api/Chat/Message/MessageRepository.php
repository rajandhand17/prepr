<?php

namespace App\Repositories\Api\Chat\Message;

use App\Helpers\UtilityHelper;
use App\Services\Chat\MessageService;
use Exception;
use Illuminate\Support\Facades\DB;

class MessageRepository implements MessageInterface
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function list(int $conversationId)
    {
        try {
            return $this->messageService->list($conversationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function send(array $data, $conversationId)
    {
        try {
            $payload = [
                'conversation_id' => $conversationId,
                'message'         => data_get($data, 'message'),
            ];

            return $this->messageService->send($payload);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getByMessageUUID($uuid)
    {
        try {
            return $this->messageService->getByMessageUUID($uuid);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteMessage($data)
    {
        try {
            DB::beginTransaction();
            $deleteMessage = $this->messageService->deleteMessage($data);
            if ($deleteMessage == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }
}
