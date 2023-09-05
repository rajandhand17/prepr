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
            'email'                  => 'rajan@amazon.com',
            'password'               => 'Prepr@123',
            'slug'                   => 'creating-the-lab-programs',
            'not_exists_slug'        => 'creating-the-lab-program_not_exists',
            'title'                  => 'Creating the Lab Programs test',
            'wrong_title'            => 'Creating the Lab Programs with another title',
            'lab_id[]'               => ['1'],
            'description'            => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'privacy'                => 'yes',
            'status'                 => 'published',
            'is_auto_created'        => 'no',
            'prize'                  => 'prize',
            'points'                 => '1000',
            'trophy'                 => 'Trophy',
            'organization_id'        => '46',
            'category_id'            => '1',
            'duration_id'            => '1',
            'level_id'               => '1',
            'achievement_name'       => 'Achievement Name',
            'achievement_points'     => '1000',
            'achievement_condition[]'=> ['1'],
            'skills[]'               => ['1'],
            'tags[]'                 => ['1'],
            'skill_groups[]'         => ['1'],
            'skill_stacks[]'         => ['1'],
            'tag_groups[]'           => ['1'],
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
        $response = $this->get('/api/v1/public/lab-program/creating-the-lab-programs?language=en', $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_view_negative(): void
    {
        $response = $this->get('/api/v1/public/lab-program/'.$this->parameters['not_exists_slug'].'?language=en', $this->headers);
        $response->assertStatus(404);
    }

    public function test_public_lab_program_like_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/like?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_like_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-program/like?language=en', [], $this->headers);
        $response->assertStatus(404);
    }

    public function test_public_lab_program_un_like_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/un-like?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_un_like_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/un-like?language=en', [], $this->headers);
        $response->assertStatus(400);
    }

    public function test_public_lab_program_favourite_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/favourite?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_favourite_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/favourite?language=en', [], $this->headers);
        $response->assertStatus(400);
    }

    public function test_public_lab_program_un_favourite_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/un-favourite?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_un_favourite_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/un-favourite?language=en', [], $this->headers);
        $response->assertStatus(400);
    }

    public function test_public_lab_program_share_positive(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/share?language=en', [], $this->headers);
        $response->assertStatus(200);
    }

    public function test_public_lab_program_share_negative(): void
    {
        $response = $this->post('/api/v1/public/lab-program/creating-the-lab-programs/share?language=en', [], $this->headers);
        $response->assertStatus(400);
    }
}
