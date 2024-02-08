<?php

namespace App\Repositories\Api\Chat\Message;

use App\Models\Message;
use App\Services\Chat\ChatService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

readonly class MessageRepository implements MessageInterface
{

    public function __construct(private ChatService $chatService)
    {
    }

    public function listMessage(int $conversationId): LengthAwarePaginator
    {
        return $this->chatService->listChat($conversationId);
    }

    public function getMessageByUUID($uuid)
    {
        $message = Message::where('uuid', $uuid)->first();

        if (!$message) {
            throw (new ModelNotFoundException())->setModel(Message::class);
        }

        return $message;
    }

    public function sendChat(array $data, $conversationId): Model|Builder
    {
        $payload = [
            "conversation_id" => $conversationId,
            "message" => $data['message']
        ];

        return $this->chatService->sendChat($payload);
    }
}
