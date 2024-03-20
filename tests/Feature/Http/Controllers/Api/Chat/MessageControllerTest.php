<?php

namespace Tests\Feature\Http\Controllers\Api\Chat;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Class MessageControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Chat\MessageController
 */
final class MessageControllerTest extends ChatTestCase
{
    public function test_message_listing_with_invalid_conversation_uuid_negative(): void
    {
        $this->get('/api/v1/chat/conversation/invaliduuid/message?language=en')->assertNotFound();
    }

    public function test_message_listing_with_no_language_params_negative(): void
    {
        $createdUuid = $this->getCreatedConversationUuid();
        $this->get("/api/v1/chat/conversation/$createdUuid/message")
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('message', __('responses.provide_language'))
                ->etc()
            );
    }

    public function test_message_listing_with_valid_uuid_positive()
    {
        $createdUuid = $this->getCreatedConversationUuid();
        $this->get("/api/v1/chat/conversation/$createdUuid/message?language=en")
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'message'])->where('success', true)
            );
    }

    public function test_message_listing_when_one_message_is_created_positive()
    {
        $createdUuid = $this->getCreatedConversationUuid();
        $this->post("/api/v1/chat/conversation/$createdUuid/message/create?language=en", [
            'message' => 'this is message',
        ])->json();

        $this->get("/api/v1/chat/conversation/$createdUuid/message?language=en")
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['data', 'success', 'message'])->has('data.list', 1)
            );
    }

    public function test_message_create_with_invalid_language_params_negative()
    {
        $createdUuid = $this->getCreatedConversationUuid();
        $this->post("/api/v1/chat/conversation/$createdUuid/message/create", [
            'message' => 'this is message',
        ])->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('message', __('responses.provide_language'))
                ->etc()
            );
    }

    public function test_message_create_with_invalid_conversation_uuid_negative()
    {
        $this->post('/api/v1/chat/conversation/invaliduuid/message/create?language=en', [
            'message' => 'this is message',
        ])->assertNotFound()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['success', 'message'])
                ->where('message', __('responses.conversation_not_found'))
                ->where('success', false)
                ->etc()
            );
    }

    public function test_message_create_without_data_negative()
    {
        $createdUuid = $this->getCreatedConversationUuid();

        $this->post("/api/v1/chat/conversation/$createdUuid/message/create?language=en")->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->hasAll(['message', 'success', 'data'])
                    ->where('success', false)
                    ->where('message', 'Validation errors')
                    ->has('data.message', fn (AssertableJson $json) => $json->where('0', __('responses.message_without_attachment')))
                    ->has('data.attachment', fn (AssertableJson $json) => $json->where('0', __('responses.attachment_without_message')))
            );
    }

    public function test_message_create_positive()
    {
        $createdUuid = $this->getCreatedConversationUuid();
        Storage::fake('attachments');
        $file = UploadedFile::fake()->image('attachment.jpg');
        $this->post("/api/v1/chat/conversation/$createdUuid/message/create?language=en", [
            'message'    => 'this is message',
            'attachment' => [$file],
        ])->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)->etc());
    }
}
