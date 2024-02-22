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
    public function create(array $data): Model|Collection|Builder|array|null
    {
        return $this->conversationService->create($data);
    }

    public function getByUUID(string $uuid)
    {
        return $this->conversationService->getByUUID($uuid);
    }

    public function list(string $type): LengthAwarePaginator
    {
        return $this->conversationService->list($type);
    }

    /**
     * @throws \Exception
     */
    public function archiveOrSeenOrDelete(string $uuid, string $action): string
    {
        return $this->conversationService->archiveOrSeenOrDelete($uuid, $action);
    }
}
