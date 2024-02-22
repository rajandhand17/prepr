<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Chat\CreateMessageRequest;
use App\Http\Resources\Chat\MessageResource;
use App\Repositories\Api\Chat\Conversation\ConversationInterface;
use App\Repositories\Api\Chat\Message\MessageInterface;

class MessageController extends AppBaseController
{
    public function __construct(private readonly MessageInterface $messageRepository, private readonly ConversationInterface $conversationRepository)
    {
    }

    public function index(string $conversation_uuid)
    {
        $conversation = $this->conversationRepository->getConversationByUUID($conversation_uuid);
        $chat = $this->messageRepository->listMessage($conversation->id);

        $responseData = [
            'total_count' => $chat->total(),
            'per_page' => $chat->perPage(),
            'count' => $chat->count(),
            'current_page' => $chat->currentPage(),
            'total_pages' => $chat->lastPage(),
            'list' => MessageResource::collection($chat->items())
        ];

        return $this->sendResponse($responseData, "Chat fetched successfully");
    }

    public function store(string $conversation_uuid, CreateMessageRequest $request)
    {
        $conversation = $this->conversationRepository->getConversationByUUID($conversation_uuid);
        return $this->messageRepository->sendChat($request->validated(), $conversation->id);
    }
}
