<?php

namespace Tests\Feature\Http\Controllers\Api\Setting;

use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\BaseTestCase;

/**
 * Class SettingControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Setting\SettingController
 */
final class SettingControllerTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->parameters = [
            ...$this->parameters,
            'invalid_password_body_params' => [
                'diff_password' => [
                    'password'              => 'Test@1234Changed',
                    'password_confirmation' => 'Test@1234Different',
                ],
                'same_password_as_previous' => [
                    'password'              => $this->parameters['password'],
                    'password_confirmation' => $this->parameters['password'],
                ],
                'less_than_six_character' => [
                    'password'              => 'Hel@1',
                    'password_confirmation' => 'Hel@1',
                ],
                'only_password_field' => [
                    'password' => 'Test@1234Changed',
                ],
            ],
            'invalid_privacy_update_data' => [
                'profile_visibility'     => 'not_valid_data',
                'project_visibility'     => 'not_valid_data',
                'friend_request_privacy' => 'not_valid_data',
            ],
            'valid_account_body_params' => [
                'first_name'              => 'Test',
                'last_name'               => 'Admin Edited',
                'username'                => 'TestAdminEdited',
                'email'                   => 'testprepradminedited@gmail.com',
                'phone_number'            => '9876543216',
                'preferred_timezone'      => 'CST',
                'preferred_language'      => 'en',
                'two_factor_verification' => 'yes',
            ],
            'valid_password_body_params' => [
                'password'              => 'Test@1234New',
                'password_confirmation' => 'Test@1234New',
            ],
            'valid_privacy_update_params' => [
                'profile_visibility'     => 'public',
                'project_visibility'     => 'public',
                'friend_request_privacy' => 'public',
            ],
            'invalid_notification_update_params' => [
                'communication'            => 'invalid_enum',
                'network_summary'          => 'invalid_enum',
                'lab_summary'              => 'invalid_enum',
                'challenge_summary'        => 'invalid_enum',
                'challenge_recommendation' => 'invalid_enum',
            ],
            'valid_notification_update_params' => [
                'full' => [
                    'communication'            => 'unsubscribe',
                    'network_summary'          => 'unsubscribe',
                    'lab_summary'              => 'unsubscribe',
                    'challenge_summary'        => 'weekly',
                    'challenge_recommendation' => 'weekly',
                ],
                'partial' => [
                    'challenge_summary' => 'weekly',
                ],
            ],
        ];
    }

    public function test_account_update_without_language_params_negative()
    {
        $this->post('/api/v1/setting/account/update', (array) $this->parameters['valid_account_body_params'])
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_account_update_without_required_data_negative()
    {
        $this->post('/api/v1/setting/account/update?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data.username', fn (AssertableJson $json) => $json->where('0', __('responses.required_field')))
                    ->has('data.email', fn (AssertableJson $json) => $json->where('0', __('responses.email_field_required')))
                    ->where('success', false)
                    ->etc()
            );
    }

    public function test_account_update_with_valid_data_positive()
    {
        $this->post('/api/v1/setting/account/update?language=en', (array) $this->parameters['valid_account_body_params'])
            ->assertOk()->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.update_user_account_successful'))
                    ->where('data.id', auth()->user()->id)
                    ->where('data.last_name', $this->parameters['valid_account_body_params']['last_name'])
                    ->where('success', true)
                    ->etc()
            );
        $this->assertDatabaseHas('users', [
            'username' => $this->parameters['valid_account_body_params']['username'],
            'email'    => $this->parameters['valid_account_body_params']['email'],
        ]);
    }

    public function test_password_update_with_same_password_as_previous_password_negative()
    {
        $this->post('/api/v1/setting/password/update?language=en', (array) $this->parameters['invalid_password_body_params']['same_password_as_previous'])
            ->assertUnprocessable()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('success', false)
                ->where('message', __('responses.same_password')));
    }

    public function test_password_update_with_different_confirmation_password_negative()
    {
        $this->post('/api/v1/setting/password/update?language=en', (array) $this->parameters['invalid_password_body_params']['diff_password'])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('data.password_confirmation', fn (AssertableJson $json) => $json->where('0', __('responses.match_confirmed_password')))->etc()
            );
    }

    public function test_password_update_with_less_than_six_character_negative()
    {
        $this->post('/api/v1/setting/password/update?language=en', (array) $this->parameters['invalid_password_body_params']['less_than_six_character'])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('data.password', fn (AssertableJson $json) => $json->where('0', __('responses.min_content_6')))->etc()
            );
    }

    public function test_password_update_without_required_field_negative()
    {
        $this->post('/api/v1/setting/password/update?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('data.password', fn (AssertableJson $json) => $json->where('0', __('responses.password_required_field')))
                    ->has('data.password_confirmation', fn (AssertableJson $json) => $json->where('0', __('responses.password_confirmation_required_field')))
                    ->etc()
            );

        $this->post('/api/v1/setting/password/update?language=en', (array) $this->parameters['invalid_password_body_params']['only_password_field'])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('data.password_confirmation', fn (AssertableJson $json) => $json->where('0', __('responses.password_confirmation_required_field')))->etc()
            );
    }

    public function test_password_update_with_valid_data_positive()
    {
        $this->post('/api/v1/setting/password/update?language=en', (array) $this->parameters['valid_password_body_params'])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.password_change_successfully'))
                    ->etc()
            );
        $this->assertTrue(Hash::check($this->parameters['valid_password_body_params']['password'], auth()->user()->getAuthPassword()));
    }

    public function test_privacy_update_with_invalid_data_negative()
    {
        $this->post('/api/v1/setting/privacy/update?language=en', (array) $this->parameters['invalid_privacy_update_data'])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.profile_visibility', fn (AssertableJson $json) => $json->where('0', __('responses.profile_privacy_in')))
                    ->has('data.project_visibility', fn (AssertableJson $json) => $json->where('0', __('responses.public_or_private')))
                    ->etc()
            );
    }

    public function test_privacy_update_without_required_field_negative()
    {
        $this->post('/api/v1/setting/privacy/update?language=en', [])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.profile_visibility', fn (AssertableJson $json) => $json->where('0', __('responses.required_fields')))
                    ->has('data.project_visibility', fn (AssertableJson $json) => $json->where('0', __('responses.public_or_private')))
                    ->etc()
            );
    }

    public function test_privacy_update_with_valid_data_positive()
    {
        $this->assertDatabaseCount('user_settings', 0);
        $this->post('/api/v1/setting/privacy/update?language=en', (array) $this->parameters['valid_privacy_update_params'])
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('data.privacy.profile_visibility', $this->parameters['valid_privacy_update_params']['profile_visibility'])
                    ->where('data.privacy.project_visibility', $this->parameters['valid_privacy_update_params']['project_visibility'])
                    ->where('data.privacy.friend_request_privacy', $this->parameters['valid_privacy_update_params']['friend_request_privacy'])
                    ->where('message', __('responses.update_privacy_successfully'))
                    ->etc()
            );
        $this->assertDatabaseCount('user_settings', 1);
    }

    public function test_notification_update_with_valid_data_positive()
    {
        $validData = (array) $this->parameters['valid_notification_update_params']['full'];
        $this->assertDatabaseCount('user_settings', 0);
        $this->post('/api/v1/setting/notification/update?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.update_notification_successfully'))
                    ->where('data.notification.communication', $validData['communication'])
                    ->where('data.notification.network_summary', $validData['network_summary'])
                    ->where('data.notification.challenge_recommendation', $validData['challenge_recommendation'])
                    ->etc()
            );
        $this->assertDatabaseCount('user_settings', 1);
    }

    public function test_notification_update_with_invalid_enum_data_negative()
    {
        $this->assertDatabaseCount('user_settings', 0);
        $this->post('/api/v1/setting/notification/update?language=en', (array) $this->parameters['invalid_notification_update_params'])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', false)
                    ->has('data.network_summary', fn (AssertableJson $json) => $json->where('0', __('responses.subscribed_or_unsubscribed_in')))
                    ->has('data.communication', fn (AssertableJson $json) => $json->where('0', __('responses.subscribed_or_unsubscribed_in')))
                    ->etc()
            );
        $this->assertDatabaseCount('user_settings', 0);
    }

    public function test_notification_update_with_partial_valid_data_positive()
    {
        //todo:test again
        $validData = (array) $this->parameters['valid_notification_update_params']['partial'];
        $this->assertDatabaseCount('user_settings', 0);
        $this->post('/api/v1/setting/notification/update?language=en', $validData)
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.update_notification_successfully'))
                    ->where('data.notification.challenge_summary', $validData['challenge_summary'])
                    ->etc()
            );
        $this->assertDatabaseCount('user_settings', 1);
    }

    public function test_delete_profile_image_positive()
    {
        $this->delete('/api/v1/setting/image/delete?language=en')
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.remove_profile_successfully'))
                    ->where('data.id', auth()->user()->id)
            );
        $this->assertDatabaseHas('users', ['id' => auth()->user()->id, 'profile_image' => config('site-settings.default_user_profile_image')]);
    }

    public function test_delete_profile_image_without_language_param_negative()
    {
        $this->delete('/api/v1/setting/image/delete')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }

    public function test_deactivate_account_positive()
    {
        $this->assertDatabaseHas('users', ['id' => auth()->user()->id, 'is_deactivated' => '0']);

        $this->post('/api/v1/setting/account/deactivate?language=en')
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('success', true)
                    ->where('message', __('responses.account_deactivate_successfully'))
                    ->etc()
            );

        $this->assertDatabaseHas('users', ['id' => auth()->user()->id, 'is_deactivated' => '1']);
    }

    public function test_deactivate_account_without_language_param_negative()
    {
        $this->post('/api/v1/setting/account/deactivate')
            ->assertBadRequest()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('message', __('responses.provide_language'))
                    ->where('success', false)
            );
    }
}
