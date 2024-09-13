<?php

namespace App\Repositories\Api\Chat\Conversation;

interface ConversationInterface
{
    public function create(array $data);

    public function getByUUID(string $uuid);

    public function list(string $type);

    public function archiveOrUnarchiveOrSeenOrDelete(string $uuid, string $action);

    public function onlineOrOffline($id, $action);
}
