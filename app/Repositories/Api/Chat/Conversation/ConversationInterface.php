<?php

namespace App\Repositories\Api\Chat\Conversation;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ConversationInterface
{
    public function createConversation(array $data): Model|Collection|Builder|array|null;

    public function getConversationByUUID(string $uuid);

    public function listConversation(): LengthAwarePaginator;

    public function archiveConversation(string $uuid): int;

    public function deleteConversation(string $uuid): void;

    public function markAsSeen($conversationId, $userId, $messageId): Model|Builder;
}
