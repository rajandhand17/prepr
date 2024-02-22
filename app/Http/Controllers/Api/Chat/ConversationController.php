<?php

namespace App\Http\Controllers\Api\Chat;

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

    public function index(): JsonResponse
    {
        $conversations = $this->conversationRepository->listConversation();

        $responseData = [
            'total_count' => $conversations->total(),
            'per_page' => $conversations->perPage(),
            'count' => $conversations->count(),
            'current_page' => $conversations->currentPage(),
            'total_pages' => $conversations->lastPage(),
            'list' => ConversationResource::collection($conversations->items())
        ];

        return $this->sendResponse($responseData, "Conversation list");
    }

    /**
     * @throws \Exception
     */
    public function create(CreateConversationRequest $request): JsonResponse
    {
        $conversation = $this->conversationRepository->createConversation($request->validated());

        return $this->sendResponse(new ConversationResource($conversation), "Conversation created");
    }

    public function archive($uuid)
    {
        $this->conversationRepository->archiveConversation($uuid);

        return $this->sendResponse(null, 'Archived successfully');
    }

    public function markAsSeen(string $uuid)
    {
        $conversation = $this->conversationRepository->getConversationByUUID($uuid);

        $lastMessage = $conversation->lastMessage()->first();

        if (!$lastMessage) {
            return $this->sendError("there is not messages in the conversations.", 403);
        }

        $data = $this->conversationRepository->markAsSeen($conversation->id, auth()->user()->id, $lastMessage->id);

        return $this->sendResponse($data, "marked conversation as seen");
    }

    public function destroy($uuid)
    {
        $this->conversationRepository->deleteConversation($uuid);

        return $this->sendResponse(null, 'Conversation Deleted');
    }

}
