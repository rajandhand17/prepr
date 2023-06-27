<?php

namespace App\Repositories\Api\User;

use App\Services\UserService;

class UserRepository implements UserInterface
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getUsers($request)
    {
        try {
            return  $this->userService->getUsers($request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
