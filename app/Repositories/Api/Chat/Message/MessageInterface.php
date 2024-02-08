<?php

namespace App\Repositories\Api\Chat\Message;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface MessageInterface
{
    public function listMessage(int $conversationId): LengthAwarePaginator;

    public function getMessageByUUID($uuid);

    public function sendChat(array $data, $conversationId): Model|Builder;



}
