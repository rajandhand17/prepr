<?php

namespace Tests\Feature;

use App\Helpers\UtilityHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ExploreControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->parameters = [
            'language'               => 'en',
            'email'                  => 'schagparprepr@gmail.com',
            'password'               => 'Test@1234',
            'slug'                   => 'un-sdg-lab-1',
            'not_exists_slug'        => 'un-sdg-lab-2',
        ];
        $this->baseUrl = '/api/v1/manage/';
        Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;

        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }
    public function test_explore_recommended_positive(): void
    {
        $response = $this->get('/api/v1/explore/recommended?language=en',$this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data']['labs'][0]);
            $this->assertArrayHasKey('language', $data['data']['labs'][0]);
            $this->assertArrayHasKey('title', $data['data']['labs'][0]);
            $this->assertArrayHasKey('slug', $data['data']['labs'][0]);
            $this->assertArrayHasKey('description', $data['data']['labs'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['labs'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['labs'][0]);
            $this->assertArrayHasKey('media', $data['data']['labs'][0]);
            $this->assertArrayHasKey('category_id', $data['data']['labs'][0]);
            $this->assertArrayHasKey('category', $data['data']['labs'][0]);
            $this->assertArrayHasKey('organization_id', $data['data']['labs'][0]);
            $this->assertArrayHasKey('organization', $data['data']['labs'][0]);
            $this->assertArrayHasKey('duration', $data['data']['labs'][0]);
            $this->assertArrayHasKey('duration_id', $data['data']['labs'][0]);
            $this->assertArrayHasKey('level', $data['data']['labs'][0]);
            $this->assertArrayHasKey('level_id', $data['data']['labs'][0]);
            $this->assertArrayHasKey('level_id', $data['data']['labs'][0]);
            $this->assertArrayHasKey('status', $data['data']['labs'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['labs'][0]);
            $this->assertArrayHasKey('skills', $data['data']['labs'][0]);
            $this->assertArrayHasKey('address', $data['data']['labs'][0]);
            $this->assertArrayHasKey('skill_groups', $data['data']['labs'][0]);
            $this->assertArrayHasKey('skill_stacks', $data['data']['labs'][0]);
            $this->assertArrayHasKey('tags', $data['data']['labs'][0]);
            $this->assertArrayHasKey('tag_groups', $data['data']['labs'][0]);
            $this->assertArrayHasKey('likes', $data['data']['labs'][0]);
            $this->assertArrayHasKey('shares', $data['data']['labs'][0]);
            $this->assertArrayHasKey('joined', $data['data']['labs'][0]);
            $this->assertArrayHasKey('liked', $data['data']['labs'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['labs'][0]);
            $this->assertArrayHasKey('lab_address', $data['data']['labs'][0]);
            $this->assertArrayHasKey('lab_achievement', $data['data']['labs'][0]);
            $this->assertArrayHasKey('lab_external_links', $data['data']['labs'][0]);
        }
    }


    public function test_explore_recommended_without_language_negative(): void
    {
        $response = $this->get('/api/v1/explore/recommended',$this->headers);
        $response->assertStatus(400);
    }

    public function test_explore_skill_positive(): void
    {
        $response = $this->get('/api/v1/explore/recommended/skills?language=en',$this->headers);
        $response->assertStatus(200);
        $data = $response->json();
    }
}
