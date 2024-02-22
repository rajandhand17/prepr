<?php

namespace App\Repositories\Api\Chat\Message;

use App\Services\Chat\MessageService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

readonly class MessageRepository implements MessageInterface
{

    public function __construct(private MessageService $messageService)
    {
    }

    public function list(int $conversationId)
    {
        return $this->messageService->list($conversationId);
    }

    public function send(array $data, $conversationId)
    {
        $payload = [
            "conversation_id" => $conversationId,
            "message" => $data['message']
        ];

        return $this->messageService->send($payload);
    }
}
