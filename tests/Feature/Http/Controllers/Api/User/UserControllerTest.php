<?php

namespace Tests\Feature\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\BaseTestCase;

/**
 * Class UserControllerTest.
 *
 * @covers \App\Http\Controllers\Api\User\UserController
 */
final class UserControllerTest extends BaseTestCase
{
    public function test_users_listing_positive(): void
    {
        $this->get('/api/v1/user?language=en')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)->etc());
    }

    public function test_users_listing_without_language_params()
    {
        $this->get('/api/v1/user')
            ->assertBadRequest()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('success', false)
                ->where('message', __('responses.provide_language')));
    }

    public function test_users_listing_with_search_params_positive()
    {
        $user = User::first();
        $this->get("/api/v1/user?language=en&search=$user->username")->assertJson(fn (AssertableJson $json) => $json->has('data', 1)->etc());
    }

    public function test_users_listing_with_search_params_that_does_not_return_any_users_positive()
    {
        $this->get('/api/v1/user?language=en&search=randomsearchtext')->assertJson(fn (AssertableJson $json) => $json->has('data', 0)->etc());
    }

    public function test_get_logged_in_users_positive(): void
    {
        $this->get('/api/v1/user/logged-in/details?language=en')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('data.email', $this->parameters['email'])->etc());
    }

    public function test_get_logged_in_users_without_language_param_positive()
    {
        $this->get('/api/v1/user/logged-in/details')
            ->assertBadRequest()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('success', false)
                ->where('message', __('responses.provide_language')));
    }
}
