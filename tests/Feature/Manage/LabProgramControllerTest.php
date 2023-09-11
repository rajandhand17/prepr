<?php

namespace Tests\Feature\Manage;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LabProgramControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->parameters = [
            'language'               => 'en',
            'title'                  => 'lorem ipsum lroe',
            'lab_id'                 => ['0bKnoTg14s'],
            'description'            => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'privacy'                => 'yes',
            'status'                 => 'published',
            'email'                  => 'schagpar@gmail.com',
            'password'               => 'Test@1234',
            'slug'                   => 'lorem-ipsum-lroe',
            'wrong_slug'             => 'lorem-ipsum-lroe-not-exists',
            'wrong_title'            => 'Lorem ipsum dolor',
            'organization_id'        => 'OA3fsgv5EK',
            'wrong_organization_id'  => 'OA3fsgv5EKQs',
            'category_id'            => '1',
            'duration_id'            => '1',
            'level_id'               => '1',
            'skills'                 => ['1'],
            'tags'                   => ['1'],
            'skill_groups'           => ['1'],
            'skill_stacks'           => ['1'],
            'tag_groups'             => ['1'],
            'is_sequential'          => 'yes',
            'is_achievement_enabled' => 'no',
            'achievement_name'       => 'Achievement Name',
            'achievement_points'     => '1000',
            'achievement_condition'  => ['1'],
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

    public function test_lab_program_manage_create_positive(): void
    {
        $response = $this->post('/api/v1/manage/lab-program/create', $this->parameters, $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_program_manage_create_negative(): void
    {
        $response = $this->post('/api/v1/manage/lab-program/create', $this->parameters, $this->headers);
        $response->assertStatus(422);
    }

    public function test_lab_program_manage_check_title_positive(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-title/'.$this->parameters['wrong_title'].'?language=en', $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_program_manage_check_title_negative(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-title/'.$this->parameters['title'].'?language=en', $this->headers);
        $response->assertStatus(403);
    }

    public function test_lab_program_manage_check_slug_positive(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-slug/'.$this->parameters['wrong_slug'].'?language=en', $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_program_manage_check_slug_negative(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-slug/'.$this->parameters['slug'].'?language=en', $this->headers);
        $response->assertStatus(400);
    }

    public function test_lab_program_manage_list_positive(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/?language=en&organization_id[]='.$this->parameters['organization_id'], $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('lab', $data['data']['list'][0]);
            $this->assertArrayHasKey('organization', $data['data']['list'][0]);
            $this->assertArrayHasKey('category_id', $data['data']['list'][0]);
            $this->assertArrayHasKey('user_id', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
        }
    }

    public function test_lab_program_manage_list_negative(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/?language=en&organization_id[]='.$this->parameters['wrong_organization_id'], $this->headers);
        $response->assertStatus(404);
    }

    public function test_lab_program_manage_view_positive(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/'.$this->parameters['slug'].'?language=en', $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_program_manage_view_negative(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/'.$this->parameters['wrong_slug'].'?language=en', $this->headers);
        $response->assertStatus(404);
    }

    public function test_lab_program_manage_update_positive(): void
    {
        $this->parameters['_method'] = 'put';
        $this->parameters['title'] = 'Lorem ipsum dolor';
        $response = $this->post('/api/v1/manage/lab-program/'.$this->parameters['slug'].'/update?language=en', $this->parameters, $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_program_manage_update_negative(): void
    {
        $response = $this->put('/api/v1/manage/lab-program/'.$this->parameters['wrong_slug'].'/update', $this->parameters, $this->headers);
        $response->assertStatus(404);
    }
}
