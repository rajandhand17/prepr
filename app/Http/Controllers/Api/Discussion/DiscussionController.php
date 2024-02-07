<?php

namespace App\Http\Controllers\Api\Discussion;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Profile\ProfileRepository;

class DiscussionController extends AppBaseController
{
    private $discussionRepository;

    public function __construct( $discussionRepository)
    {
        $this->discussionRepository = $discussionRepository;
    }
}
