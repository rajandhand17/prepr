<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Chat\CreateChatRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Repositories\Api\Chat\Conversation\ConversationRepository;
use App\Repositories\Api\Chat\Message\MessageRepository;

class MessageController extends AppBaseController
{
    public function __construct(private readonly MessageRepository $inboxRepository, private readonly ConversationRepository $conversationRepository)
    {
    }

    public function index(string $conversation_uuid)
    {
        $conversation = $this->conversationRepository->getConversationByUUID($conversation_uuid);
        $chat = $this->inboxRepository->listMessage($conversation->id);

        $responseData = [
            'total_count' => $chat->total(),
            'per_page' => $chat->perPage(),
            'count' => $chat->count(),
            'current_page' => $chat->currentPage(),
            'total_pages' => $chat->lastPage(),
            'list' => ChatResource::collection($chat->items())
        ];

        return $this->sendResponse($responseData, "Chat fetched successfully");
    }

    public function store(string $conversation_uuid, CreateChatRequest $request)
    {
        $conversation = $this->conversationRepository->getConversationByUUID($conversation_uuid);
        return $this->inboxRepository->sendChat($request->validated(), $conversation->id);
    }
}
