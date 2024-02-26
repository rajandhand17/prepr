<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Chat\CreateMessageRequest;
use App\Http\Resources\Chat\MessageResource;
use App\Repositories\Api\Chat\Conversation\ConversationInterface;
use App\Repositories\Api\Chat\Message\MessageInterface;
use Exception;

class MessageController extends AppBaseController
{
    public function __construct(private readonly MessageInterface $messageRepository, private readonly ConversationInterface $conversationRepository)
    {
    }

    public function index(string $conversation_uuid)
    {
        try {
            $conversation = $this->conversationRepository->getByUUID($conversation_uuid);
            if (!$conversation) {
                return $this->sendError(__('responses.conversation_not_found'), 404);
            }

            $chat = $this->messageRepository->list($conversation->id);

            if ($chat) {
                $responseData = [
                    'total_count' => $chat->total(),
                    'per_page' => $chat->perPage(),
                    'count' => $chat->count(),
                    'current_page' => $chat->currentPage(),
                    'total_pages' => $chat->lastPage(),
                    'list' => MessageResource::collection($chat->items())
                ];

                return $this->sendResponse($responseData, __("response.found_message_list"));
            }

            return $this->sendError(__('responses.not_found_message_list'), 404);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function store(string $conversation_uuid, CreateMessageRequest $request)
    {
        try {
            $conversation = $this->conversationRepository->getByUUID($conversation_uuid);

            if (!$conversation) {
                return $this->sendError(__('responses.conversation_not_found'), 404);
            }

            $message = $this->messageRepository->send($request->validated(), $conversation->id);

            if (!$message) {
                return $this->sendError(__('responses.message_not_created'), 409);
            }

            return $this->sendResponse($message, __("response.message_created"));

        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
