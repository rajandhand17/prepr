<?php

namespace Tests\Feature\Http\Controllers\Api\Chat;

use Tests\BaseTestCase;

class ChatTestCase extends BaseTestCase
{
    protected $parameters;

    public function setUp(): void
    {
        parent::setUp();
        $this->parameters = [
            'email'                    => 'testprepradmin@gmail.com',
            'password'                 => 'Test@1234',
            'group_usernames'          => ['testchallengemanager', 'testlabmanager', 'testusers'],
            'direct_message_usernames' => ['testchallengemanager'],
        ];
    }

    protected function getCreatedConversationUuid()
    {
        $createdData = $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => $this->parameters['group_usernames'],
            'type'      => 'message',
        ])->json();

        return data_get($createdData, 'data.uuid');
    }
}
