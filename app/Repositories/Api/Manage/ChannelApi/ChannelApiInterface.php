<?php

namespace App\Repositories\Api\Manage\ChannelApi;

interface ChannelApiInterface
{
    public function getLabs($type, $organization, $user);

    public function getChallenges($type, $organization, $user);

    public function assignUserToLab($users, $lab);
}
