<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResourceModuleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function setUp(): void
    {
        parent::setUp();
        $this->parameters = [
            'language'        => 'en',
            'wrong_language'  => 'hindi',
            'slug'            => 'prepr',
            'wrong_slug'      => 'wrong_slug',
            'email'           => 'salar.chagparprepr@prepr.org',
            'password'        => 'Test@1234',
            'review'=>4,
        ];
        Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;
        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }

    public function test_resource_module_positive_test(): void
    {
        $response = $this->get('/api/v1/public/resource-module/?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('user', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('cover_image', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('is_global', $data['data']['list'][0]);
        }
    }

    public function test_resource_module_negative_test(): void
    {
        $response = $this->get('/api/v1/public/resource-module/?language=Hindi');
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_resource_module_view_positive_test(): void
    {
        $response = $this->get('/api/v1/public/resource-module/resource-module/?language=en');
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data']);
            $this->assertArrayHasKey('language', $data['data']);
            $this->assertArrayHasKey('title', $data['data']);
            $this->assertArrayHasKey('user', $data['data']);
            $this->assertArrayHasKey('organization_id', $data['data']);
            $this->assertArrayHasKey('slug', $data['data']);
            $this->assertArrayHasKey('description', $data['data']);
            $this->assertArrayHasKey('media_type', $data['data']);
            $this->assertArrayHasKey('cover_image', $data['data']);
            $this->assertArrayHasKey('privacy', $data['data']);
            $this->assertArrayHasKey('status', $data['data']);

        }
    }

    public function test_resource_module_view_negative_test(): void
    {
        $response = $this->get('/api/v1/public/resource-module/resource-moduless/?language=en');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_resource_module_add_review_positive_test(): void
    {
        $response = $this->post('/api/v1/public/resource-module/resource-module/add-review?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_resource_module_add_review_negative_test(): void
    {
        $response = $this->post('/api/v1/public/resource-module/resource-module/add-review?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

}
