<?php

namespace App\Repositories\Api\Chat\Conversation;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ConversationInterface
{

    public function create(array $data);

    public function getByUUID(string $uuid);

    public function list(string $type);

    public function archiveOrSeenOrDelete(string $uuid, string $action);

    public function onlineOrOffline($id , $action);
}
