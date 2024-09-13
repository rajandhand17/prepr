<?php

namespace App\Http\Controllers\Api\Chat;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Chat\CreateConversationRequest;
use App\Http\Resources\Chat\ConversationResource;
use App\Repositories\Api\Chat\Conversation\ConversationRepository;
use Exception;
use Illuminate\Http\JsonResponse;

class ConversationController extends AppBaseController
{
    private $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function index(string $type)
    {
        try {
            if (!in_array($type, ['inbox', 'archive'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }

            $conversations = $this->conversationRepository->list($type);
            if ($conversations) {
                $responseData = [
                    'total_count'  => $conversations->total(),
                    'per_page'     => $conversations->perPage(),
                    'count'        => $conversations->count(),
                    'current_page' => $conversations->currentPage(),
                    'total_pages'  => $conversations->lastPage(),
                    'list'         => ConversationResource::collection($conversations->items()),
                ];

                return $this->sendResponse($responseData, __('responses.list_conversation'));
            }

            return $this->sendError(__('responses.not_found_conversation_list'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateConversationRequest $request): JsonResponse
    {
        try {
            $conversation = $this->conversationRepository->create($request->validated());

            if ($conversation) {
                return $this->sendResponse(new ConversationResource($conversation), __('responses.conversation_created'));
            }

            return $this->sendError(__('responses.conversation_stored_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function archiveOrUnarchiveOrSeenOrDelete(string $uuid, string $action)
    {
        try {
            if (!in_array($action, ['archive', 'un-archive', 'seen', 'delete'])) {
                return $this->sendError(__('responses.handler_bad_request'), 402);
            }
            $conversation = $this->conversationRepository->getByUUID($uuid);
            if (!$conversation) {
                return $this->sendError(__('responses.conversation_not_found'), 404);
            }
            if ($action == 'seen' && $conversation->chats()->count() == 0) {
                return $this->sendError(__('responses.no_message_conversation'));
            }
            $message = $this->conversationRepository->archiveOrUnarchiveOrSeenOrDelete($uuid, $action);
            if ($message) {
                return $this->sendResponse(null, __('responses.conversation_'.$action.'_successfully'));
            }

            return $this->sendError(__('responses.conversation_'.$action.'_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function onlineOrOffline($id, $action)
    {
        try {
            $onlineOrOffline = $this->conversationRepository->onlineOrOffline($id, $action);
            if (!$onlineOrOffline) {
                return $this->sendError(__('responses.mark_user_'.$action.'_failed'), 400);
            }

            return $this->sendResponse(null, __('responses.mark_user_'.$action.'_successfully'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
