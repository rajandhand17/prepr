<?php

namespace App\Http\Controllers\Api\Chat;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Chat\CreateMessageRequest;
use App\Http\Resources\Chat\MessageResource;
use App\Repositories\Api\Chat\Conversation\ConversationRepository;
use App\Repositories\Api\Chat\Message\MessageRepository;
use Exception;

class MessageController extends AppBaseController
{
    private $messageRepository;
    private $conversationRepository;

    public function __construct(MessageRepository $messageRepository, ConversationRepository $conversationRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->conversationRepository = $conversationRepository;
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
                    'total_count'  => $chat->total(),
                    'per_page'     => $chat->perPage(),
                    'count'        => $chat->count(),
                    'current_page' => $chat->currentPage(),
                    'total_pages'  => $chat->lastPage(),
                    'list'         => MessageResource::collection($chat->items()),
                ];

                return $this->sendResponse($responseData, __('responses.found_message_list'));
            }

            return $this->sendError(__('responses.not_found_message_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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

            return $this->sendResponse(new MessageResource($message), __('responses.message_created'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($message_uuid)
    {
        try {
            $message = $this->messageRepository->getByMessageUUID($message_uuid);

            if (!$message) {
                return $this->sendError(__('responses.message_not_found'), 404);
            }

            $message = $this->messageRepository->deleteMessage($message);
            if (!$message) {
                return $this->sendError(__('responses.message_not_deleted'), 409);
            }

            return $this->sendResponse([], __('responses.message_deleted'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
