<?php

namespace App\Http\Controllers\Api\Chat;

use Exception;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Chat\CreateConversationRequest;
use App\Http\Resources\Chat\ConversationResource;
use App\Repositories\Api\Chat\Conversation\ConversationInterface;
use Illuminate\Http\JsonResponse;

class ConversationController extends AppBaseController
{
    public function __construct(private readonly ConversationInterface $conversationRepository)
    {
    }

    public function index($type)
    {
        try {
            $conversations = $this->conversationRepository->list($type);

            if ($conversations) {
                $responseData = [
                    'total_count' => $conversations->total(),
                    'per_page' => $conversations->perPage(),
                    'count' => $conversations->count(),
                    'current_page' => $conversations->currentPage(),
                    'total_pages' => $conversations->lastPage(),
                    'list' => ConversationResource::collection($conversations->items())
                ];
                return $this->sendResponse($responseData, __("responses.list_conversation"));
            }
            return $this->sendError(__('responses.not_found_conversation_list'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateConversationRequest $request): JsonResponse
    {
        try {
            $conversation = $this->conversationRepository->create($request->validated());

            if ($conversation) {
                return $this->sendResponse(new ConversationResource($conversation), __("responses.conversation_created"));
            }
            return $this->sendError(__('responses.conversation_stored_failed'), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }

    }


    public function archiveOrSeenOrDelete(string $uuid, string $action)
    {
        try {
            $message = $this->conversationRepository->archiveOrSeenOrDelete($uuid, $action);

            if ($message) {
                return $this->sendResponse(null, __("responses.conversation_" . $action . "_successfully"));
            }
            return $this->sendError(__("responses.conversation_" . $action . "_failed"), 400);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

}
