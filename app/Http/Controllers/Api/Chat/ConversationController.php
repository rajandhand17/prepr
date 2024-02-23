<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Chat\CreateConversationRequest;
use App\Http\Resources\Chat\ConversationResource;
use App\Repositories\Api\Chat\Conversation\ConversationInterface;
use Illuminate\Http\JsonResponse;
use Exception;

class ConversationController extends AppBaseController
{
    public function __construct(private readonly ConversationInterface $conversationRepository)
    {
    }

    public function index($type): JsonResponse
    {
        $conversations = $this->conversationRepository->list($type);

        $responseData = [
            'total_count' => $conversations->total(),
            'per_page' => $conversations->perPage(),
            'count' => $conversations->count(),
            'current_page' => $conversations->currentPage(),
            'total_pages' => $conversations->lastPage(),
            'list' => ConversationResource::collection($conversations->items())
        ];

        return $this->sendResponse($responseData, __("response.list_conversation"));
    }

    /**
     * @throws Exception
     */
    public function create(CreateConversationRequest $request): JsonResponse
    {
        $conversation = $this->conversationRepository->create($request->validated());

        return $this->sendResponse(new ConversationResource($conversation), __("response.conversation_created"));
    }


    public function archiveOrSeenOrDelete(string $uuid, string $action)
    {
        $message = $this->conversationRepository->archiveOrSeenOrDelete($uuid, $action);

        return $this->sendResponse(null, $message);
    }

}
