<?php

namespace App\Repositories\Api\Chat\Message;

use App\Services\Chat\MessageService;
use Exception;

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
            return false;
        }
    }

    public function send(array $data, $conversationId)
    {
        try {
            $payload = [
                'conversation_id' => $conversationId,
                'message'         => $data['message'],
            ];

            return $this->messageService->send($payload);
        } catch (Exception $e) {
            return false;
        }
    }
}
