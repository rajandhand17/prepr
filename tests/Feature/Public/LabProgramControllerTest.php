<?php

namespace Tests\Feature\Public;

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
            'email'                  => 'schagpar@gmail.com',
            'password'               => 'Test@1234',
            'slug'                   => 'lorem-ipsum-lroe',
            'wrong_slug'             => 'lorem-ipsum-lroe-not-exists',
            'title'                  => 'Lorem ipsum dolor',
            'wrong_title'            => 'Lorem ipsum dolor sit amet',
            'lab_id'               => ['0bKnoTg14s'],
            'description'            => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'privacy'                => 'yes',
            'status'                 => 'published',
            'is_auto_created'        => 'no',
            'prize'                  => 'prize',
            'points'                 => '1000',
            'trophy'                 => 'Trophy',
            'organization_id'        => 'OA3fsgv5EK',
            'category_id'            => '1',
            'duration_id'            => '1',
            'level_id'               => '1',
            'achievement_name'       => 'Achievement Name',
            'achievement_points'     => '1000',
            'achievement_condition'=> ['1'],
            'skills'               => ['1'],
            'tags'                 => ['1'],
            'skill_groups'         => ['1'],
            'skill_stacks'         => ['1'],
            'tag_groups'           => ['1'],
        ];

        $this->baseUrl = '/api/v1/public/';
        Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;

        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }

    public function test_public_lab_program_view_positive(): void
    {
        $response = $this->get('/api/v1/public/lab-program/'.$this->parameters['slug'].'?language=en', $this->headers);

        $response->assertStatus(200);
    }

    public function test_public_lab_program_view_negative(): void
    {
        $response = $this->get('/api/v1/public/lab-program/'.$this->parameters['wrong_slug'].'?language=en', $this->headers);
        $response->assertStatus(404);
    }

    public function test_public_lab_program_like_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['slug'].'/like?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_like_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['wrong_slug'].'/like?language=en', [], $this->headers);
        $response->assertStatus(404);
    }

    public function test_public_lab_program_un_like_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['slug'].'/un-like?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_un_like_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['wrong_slug'].'/un-like?language=en', [], $this->headers);
        $response->assertStatus(404);
    }

    public function test_public_lab_program_favourite_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['slug'].'/favourite?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_favourite_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['wrong_slug'].'/favourite?language=en', [], $this->headers);
        $response->assertStatus(404);
    }

    public function test_public_lab_program_un_favourite_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['slug'].'/un-favourite?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_un_favourite_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['wrong_slug'].'/un-favourite?language=en', [], $this->headers);
        $response->assertStatus(404);
    }

    public function test_public_lab_program_share_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['slug'].'/share?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_share_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/'.$this->parameters['wrong_slug'].'/share?language=en', [], $this->headers);
        $response->assertStatus(404);
    }
}
