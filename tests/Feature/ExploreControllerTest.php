<?php

namespace Tests\Feature;

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
            'email2'                 => 'rajan@yupmail.com',
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
        $response = $this->get('/api/v1/explore/recommended?language=en', $this->headers);
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
            $this->assertArrayHasKey('id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('language', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('title', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('slug', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('description', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('media', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('category_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('category', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('organization_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('organization', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('duration', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('duration_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('level', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('level_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('level_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('status', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('skills', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('skill_groups', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('skill_stacks', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('tags', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('tag_groups', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('likes', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('shares', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('joined', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('liked', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['challenges'][0]);
        }
    }

    public function test_explore_recommended_without_language_negative(): void
    {
        $response = $this->get('/api/v1/explore/recommended', $this->headers);
        $response->assertStatus(400);
    }

    public function test_explore_featured_positive(): void
    {
        $response = $this->get('/api/v1/explore/featured?language=en', $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('language', $data['data'][0]);
            $this->assertArrayHasKey('title', $data['data'][0]);
            $this->assertArrayHasKey('slug', $data['data'][0]);
            $this->assertArrayHasKey('description', $data['data'][0]);
            $this->assertArrayHasKey('privacy', $data['data'][0]);
            $this->assertArrayHasKey('media_type', $data['data'][0]);
            $this->assertArrayHasKey('media', $data['data'][0]);
            $this->assertArrayHasKey('category_id', $data['data'][0]);
            $this->assertArrayHasKey('category', $data['data'][0]);
            $this->assertArrayHasKey('organization_id', $data['data'][0]);
            $this->assertArrayHasKey('organization', $data['data'][0]);
            $this->assertArrayHasKey('duration', $data['data'][0]);
            $this->assertArrayHasKey('duration_id', $data['data'][0]);
            $this->assertArrayHasKey('level', $data['data'][0]);
            $this->assertArrayHasKey('level_id', $data['data'][0]);
            $this->assertArrayHasKey('level_id', $data['data'][0]);
            $this->assertArrayHasKey('status', $data['data'][0]);
            $this->assertArrayHasKey('member_count', $data['data'][0]);
            $this->assertArrayHasKey('skills', $data['data'][0]);
            $this->assertArrayHasKey('address', $data['data'][0]);
            $this->assertArrayHasKey('skill_groups', $data['data'][0]);
            $this->assertArrayHasKey('skill_stacks', $data['data'][0]);
            $this->assertArrayHasKey('tags', $data['data'][0]);
            $this->assertArrayHasKey('tag_groups', $data['data'][0]);
            $this->assertArrayHasKey('likes', $data['data'][0]);
            $this->assertArrayHasKey('shares', $data['data'][0]);
            $this->assertArrayHasKey('joined', $data['data'][0]);
            $this->assertArrayHasKey('liked', $data['data'][0]);
            $this->assertArrayHasKey('favourite', $data['data'][0]);
            $this->assertArrayHasKey('lab_address', $data['data'][0]);
            $this->assertArrayHasKey('lab_achievement', $data['data'][0]);
            $this->assertArrayHasKey('lab_external_links', $data['data'][0]);
        }
    }

    public function test_explore_featured_without_language_negative(): void
    {
        $response = $this->get('/api/v1/explore/featured', $this->headers);
        $response->assertStatus(400);
    }

    public function test_explore_skill_positive(): void
    {
        $response = $this->get('/api/v1/explore/recommended/skills?language=en', $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertArrayHasKey('id', $data['data'][0]);
        $this->assertArrayHasKey('title', $data['data'][0]);
        $this->assertArrayHasKey('challenges', $data['data'][0]);
        $this->assertArrayHasKey('labs', $data['data'][0]);
    }

    public function test_explore_skill_without_language_negative(): void
    {
        $response = $this->get('/api/v1/explore/recommended/skills', $this->headers);
        $response->assertStatus(400);
    }

    public function test_explore_trending_labs_positive(): void
    {
        Auth::attempt(['email' =>$this->parameters['email2'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;

        $this->header = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
        $response = $this->get('/api/v1/explore/recommended/skills?language=en', $this->header);
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
            $this->assertArrayHasKey('id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('language', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('title', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('slug', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('description', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('media', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('category_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('category', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('organization_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('organization', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('duration', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('duration_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('level', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('level_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('level_id', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('status', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('skills', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('skill_groups', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('skill_stacks', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('tags', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('tag_groups', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('likes', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('shares', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('joined', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('liked', $data['data']['challenges'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['challenges'][0]);
        }
    }

    public function test_explore_skill_without_language_negatives(): void
    {
        $response = $this->get('/api/v1/explore/recommended/skills', $this->headers);
        $response->assertStatus(400);
    }
}
