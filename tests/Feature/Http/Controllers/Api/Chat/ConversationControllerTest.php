<?php

namespace Tests\Feature\Http\Controllers\Api\Chat;

use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class ConversationControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Chat\ConversationController
 */
final class ConversationControllerTest extends ChatTestCase
{
    public function test_list_conversation_without_language_params_negative()
    {
        $this->get('/api/v1/chat/conversation/non-archive')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('message', __('responses.provide_language'))
                ->where('success', false)
                ->etc()
            );
    }

    public function test_list_conversation_with_conversation_type_archive_positive()
    {
        $this->get('/api/v1/chat/conversation/archive?language=en')
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.list_conversation'))
                    ->etc()
            );
    }

    public function test_list_conversation_with_conversation_type_non_archive_positive()
    {
        $this->get('/api/v1/chat/conversation/inbox?language=en')
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.list_conversation'))
                    ->etc()
            );
    }

    public function test_list_conversation_with_conversation_type_that_does_not_exists_Negative()
    {
        $this
            ->get('/api/v1/chat/conversation/type-that-does-not-exists?language=en')
            ->assertStatus(402)
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message', __('responses.handler_bad_request'))->etc()
            );
    }

    public function test_create_conversation_by_passing_one_username_positive(): void
    {
        $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => $this->parameters['direct_message_usernames'],
            'type'      => 'message',
        ])->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message', 'data.id', 'data.uuid', 'data.type'])
                    ->where('message', __('responses.conversation_created'))
                    ->where('data.type', 'direct_message')
            );
        $this->assertDatabaseCount('conversations', 1);
    }

    public function test_create_conversation_by_passing_more_than_one_username_positive(): void
    {
        $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => $this->parameters['group_usernames'],
            'type'      => 'message',
        ])->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message', 'data.id', 'data.uuid', 'data.type'])
                    ->where('message', __('responses.conversation_created'))
                    ->where('data.type', 'group')
            );
    }

    public function test_create_conversation_without_passing_language_params_negative()
    {
        $this->post('/api/v1/chat/conversation/create', [
            'usernames' => $this->parameters['group_usernames'],
            'type'      => 'message',
        ])->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('message', __('responses.provide_language'))
                ->etc()
            );
    }

    public function test_create_conversation_without_passing_any_data_negative()
    {
        $this->post('/api/v1/chat/conversation/create?language=en')
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('success', false)
                ->where('message', 'Validation errors')
                ->has('data.usernames', fn (AssertableJson $json) => $json->where('0', __('responses.conversation_users_required')))
                ->has('data.type', fn (AssertableJson $json) => $json->where('0', __('responses.conversation_type_required')))
                ->etc()
            );
    }

    public function test_create_conversation_with_wrong_username_negative()
    {
        $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => ['user_that_does_not_exists'],
            'type'      => 'message',
        ])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('success', false)
                ->where('message', 'Validation errors')
                ->has('data.usernames', fn (AssertableJson $json) => $json->where('0', __('responses.conversation_user_exists')))
                ->etc()
            );
    }

    public function test_create_conversation_with_type_announcement_positive()
    {
        $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => $this->parameters['group_usernames'],
            'type'      => 'announcement',
        ])->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message', 'data.id', 'data.uuid', 'data.type'])
                    ->where('message', __('responses.conversation_created'))
                    ->where('data.type', 'announcement')
            );
    }

    public function test_create_conversation_with_invalid_type_negative()
    {
        $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => $this->parameters['group_usernames'],
            'type'      => 'invalid_type',
        ])->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('success', false)
                ->where('message', 'Validation errors')
                ->has('data.type', fn (AssertableJson $json) => $json->where('0', __('responses.type_in_announcement_or_message')))
                ->etc()
            );
    }

    public function test_conversation_archive_positive(): void
    {
        $createdUuid = $this->getCreatedConversationUuid();
        $this->post("/api/v1/chat/conversation/$createdUuid/archive?language=en")
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message'])->where('message', __('responses.conversation_archive_successfully'))
            );
    }

    public function test_conversation_archive_with_invalid_uuid_negative(): void
    {
        $this->post('/api/v1/chat/conversation/invaliduuid/archive?language=en')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])->where('message', __('responses.conversation_archive_failed'))
            );
    }

    public function test_conversation_delete_positive()
    {
        $createdData = $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => $this->parameters['group_usernames'],
            'type'      => 'message',
        ])->json();
        $createdUuid = $createdData['data']['uuid'];

        $this->post("/api/v1/chat/conversation/$createdUuid/delete?language=en")
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message'])->where('message', __('responses.conversation_delete_successfully'))
            );
    }

    public function test_conversation_delete_with_invalid_uuid_negative()
    {
        $this->post('/api/v1/chat/conversation/notvaliduuid/delete?language=en')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])->where('message', __('responses.conversation_delete_failed'))
            );
    }

    public function test_mark_conversation_seen_positive()
    {
        $createdData = $this->post('/api/v1/chat/conversation/create?language=en', [
            'usernames' => $this->parameters['group_usernames'],
            'type'      => 'message',
        ])->json();

        $createdUuid = $createdData['data']['uuid'];
        $this->post("/api/v1/chat/conversation/$createdUuid/seen?language=en")
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message'])->where('message', __('responses.conversation_seen_successfully'))
            );
    }

    public function test_mark_conversation_seen_with_invalid_uuid_negative()
    {
        $this->post('/api/v1/chat/conversation/notuuid/seen?language=en')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])->where('message', __('responses.conversation_seen_failed'))
            );
    }

    public function test_mark_user_online_positive()
    {
        $this->post('/api/v1/chat/conversation/user/1/online?language=en')
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message'])->where('message', __('responses.mark_user_online_successfully'))
            );
    }

    public function test_mark_user_online_without_language_param_negative()
    {
        $this->post('/api/v1/chat/conversation/user/1/online')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('message', __('responses.provide_language'))
                ->where('success', false)
                ->etc()
            );
    }

    public function test_mark_user_offline_positive()
    {
        $this->post('/api/v1/chat/conversation/user/1/offline?language=en')
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message'])->where('message', __('responses.mark_user_offline_successfully'))
            );
    }

    public function test_mark_user_offline_without_language_param_negative()
    {
        $this->post('/api/v1/chat/conversation/user/1/offline')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('message', __('responses.provide_language'))
                ->where('success', false)
                ->etc()
            );
    }
}
