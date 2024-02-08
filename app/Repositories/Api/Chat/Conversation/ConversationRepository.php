<?php

namespace App\Repositories\Api\Chat\Conversation;

use App\Models\Conversation;
use App\Services\Chat\ConversationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

readonly class ConversationRepository implements ConversationInterface
{
    public function __construct(private ConversationService $conversationService)
    {
    }

    /**
     * @throws \Exception
     */
    public function createConversation(array $data): Model|Collection|Builder|array|null
    {
        return $this->conversationService->start($data);
    }

    public function getConversationByUUID(string $uuid)
    {
        $conversation = Conversation::where('uuid', $uuid)->first();

        if (!$conversation) {
            throw (new ModelNotFoundException())->setModel(Conversation::class);
        }

        return $conversation;
    }

    public function listConversation(): LengthAwarePaginator
    {
        return $this->conversationService->listConversation();
    }

    public function archiveConversation(string $uuid): int
    {
        $conversation = $this->getConversationByUUID($uuid);
        return Conversation::where('id', $conversation->id)->update(['is_archived' => true]);
    }

    public function deleteConversation(string $uuid): void
    {
        $conversation = $this->getConversationByUUID($uuid);
        Conversation::where('id', $conversation->id)->delete();
    }

    public function markAsSeen($conversationId, $userId, $messageId): Model|Builder
    {
        return $this->conversationService->markConversationAsSeen($conversationId, $userId, $messageId);
    }
}
