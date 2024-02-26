<?php

namespace App\Repositories\Api\Chat\Message;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface MessageInterface
{
    public function list(int $conversationId);

    public function send(array $data, $conversationId);


}
