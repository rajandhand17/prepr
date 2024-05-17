<?php

namespace App\Repositories\Api\Chat\Message;

interface MessageInterface
{
    public function list(int $conversationId);

    public function send(array $data, $conversationId);

    public function getByMessageUUID($uuid);

    public function deleteMessage($data);
}
