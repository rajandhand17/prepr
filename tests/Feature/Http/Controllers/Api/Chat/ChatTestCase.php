<?php

namespace Tests\Feature\Http\Controllers\Api\Chat;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ChatTestCase extends TestCase
{

    use RefreshDatabase;

    protected $parameters;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->parameters = [
            'email' => 'testprepradmin@gmail.com',
            'password' => 'Test@1234',
            'group_usernames' => ["testchallengemanager", "testlabmanager", "testusers"],
            "direct_message_usernames" => ["testchallengemanager"]
        ];
        Auth::attempt(['email' => $this->parameters['email'], 'password' => $this->parameters['password']]);
        $user = Auth::user();
        $this->actingAs($user, 'api');
    }

    protected function getCreatedConversationUuid()
    {
        $createdData = $this->post("/api/v1/chat/conversation/create?language=en", [
            "usernames" => $this->parameters['group_usernames'],
            "type" => "message"
        ])->json();

        return data_get($createdData, "data.uuid");
    }
}
