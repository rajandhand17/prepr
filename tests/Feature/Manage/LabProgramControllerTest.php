<?php

namespace Tests\Feature\Manage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            'title'                  =>'Creating the Lab Programs test',
            'lab_id[]'               => ['1'],
            'description'            =>'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'privacy'                => 'yes',
            'status'                 =>'published',
            'email'                  => 'rajan@amazon.com',
            'password'               => 'Prepr@123',
            'slug'                   => 'creating-the-lab-program-test',
            'not_exists_slug'        => 'creating-the-lab-program_not_exists',
            'wrong_title'            =>'Creating the Lab Programs with another title',
            'is_auto_created'        =>'no',
            'prize'                  =>'prize',
            'points'                 =>'1000',
            'trophy'                 =>'Trophy',
            'organization_id'       =>'46',
            'category_id'           =>'1',
            'duration_id'           =>'1',
            'level_id'              =>'1',
            'achievement_name'      =>'Achievement Name',
            'achievement_points'    =>'1000',
            'achievement_condition[]'=>['1'],
            'skills[]'              =>['1'],
            'tags[]'                =>['1'],
            'skill_groups[]'        =>['1'],
            'skill_stacks[]'        =>['1'],
            'tag_groups[]'          =>['1'],
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

    public function test_lab_program_manage_create_positive():void
    {
        $response = $this->post('/api/v1/manage/lab-program/create',$this->parameters, $this->headers);
        $response->assertStatus(200);
    }
    public function test_lab_program_manage_create_negative():void
    {
        $response = $this->post('/api/v1/manage/lab-program/create',$this->parameters, $this->headers);
        $response->assertStatus(422);
    }
    public function test_lab_program_manage_check_title_positive(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-title/Creating the Lab Program?language=en',$this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_program_manage_check_title_negative():void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-title/'.$this->parameters['title'].'?language=en',$this->headers);
        $response->assertStatus(403);
    }

    public function test_lab_program_manage_check_slug_positive():void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-slug/creating-the-lab-program?language=en',$this->headers);
        $response->assertStatus(200);
    }
    public function test_lab_program_manage_check_slug_negative():void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-slug/creating-the-lab-programs?language=en',$this->headers);
        $response->assertStatus(400);
    }

    public function test_lab_program_manage_list_positive():void
    {
        $response = $this->get('/api/v1/manage/lab-program/?language=en', $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']){
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('lab_id', $data['data']['list'][0]);
            $this->assertArrayHasKey('user_id', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('is_auto_created', $data['data']['list'][0]);
        }
    }
    public function test_lab_program_manage_view_positive():void
    {
        $response = $this->get('/api/v1/manage/lab-program/creating-the-lab-programs?language=en', $this->headers);
        $response->assertStatus(200);
    }
    public function test_lab_program_manage_view_negative():void
    {
        $response = $this->get('/api/v1/manage/lab-program/'.$this->parameters['not_exists_slug'].'?language=en', $this->headers);
        $response->assertStatus(404);
    }

    public function test_lab_program_manage_update_positive():void
    {
        $this->parameters['_method']='put';
        $this->parameters['title']='update lab program title';
        $response = $this->post('/api/v1/manage/lab-program/creating-the-lab-programs/update?language=en',$this->parameters, $this->headers);
        $response->assertStatus(422);
    }
    public function test_lab_program_manage_update_negative():void
    {
        $this->parameters['title']='update lab program title';
        $response = $this->put('/api/v1/manage/lab-program/creating-the-lab-programs/update?language=en',$this->parameters, $this->headers);
        $response->assertStatus(422);
    }
}
