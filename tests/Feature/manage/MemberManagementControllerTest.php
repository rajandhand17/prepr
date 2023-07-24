<?php

namespace Tests\Feature\manage;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MemberManagementControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected $parameters;

    public function setUp(): void
    {
        parent::setUp();

        $this->parameters = [
            'language'       => 'en',
            'user_id'        => '2',
            'name'           => 'Amazon_update',
            'slug'           => 'amazon',
            'wrong_language' => 'Hindi',
            'page'           => '1',
            'component'      => 'organization',
            'wrong_component'=> 'component',
            'type'           => 'invite',
            'invite_type'    => 'email',
            'role'           => 'Organization Manager',
            'module_id'      => '27',
            'subject_line'   => 'Invitation to join an organization',
            'email_body'     => 'email messages',
            'email'          => 'rajan@amazon.com',
            'another_email'  => 'rajandhand17@gmail.com',
            'password'       => 'Prepr@123',
            'invite_status'  => 'pending',
            'invite_email'   => ['rajan@prepr.org'],
            'inviter_id'     => '15',
            'id'             => 'a4gqUu3lr5',
            'auto_invite'    => 'yes',
        ];
        $data = Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        Auth::attempt(['email' =>$this->parameters['another_email'], 'password' =>$this->parameters['password']]);
        $userWithoutPermission = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;
        $this->tokenWithoutPermission = $userWithoutPermission->createToken(env('APP_NAME'))->accessToken;
        
        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
        $this->headersWithoutPermission = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->tokenWithoutPermission,
        ];
    } 

    /**create member management positive */
    public function test_create_positive()
    {
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/create?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**create member management negative */
    public function test_create_negative()
    {
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/create?language='.$this->parameters['language'], [], $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**get all the roles member management positive */
    public function test_get_roles_positive()
    {
        $response = $this->get('/api/v1/manage/member-management/get-roles?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**get all the roles member management positive */
    public function test_get_roles_negative()
    {
        $response = $this->post('/api/v1/manage/member-management/get-roles?language='.$this->parameters['wrong_language'], $this->parameters, $this->headers);
        $this->assertEquals(405, $response->getStatusCode());
    }

    // /**withour permission try to acess */
    // public function test_without_permission_get_records(){
    //     $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headersWithoutPermission);
    //     $response->assertStatus(403);
    // }
    /**Listing member management positive */
    public function test_listing_positive()
    {
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data']['users'][0]);
            $this->assertArrayHasKey('type', $data['data']['users'][0]);
            $this->assertArrayHasKey('invite_type', $data['data']['users'][0]);
            $this->assertArrayHasKey('name', $data['data']['users'][0]);
            $this->assertArrayHasKey('email', $data['data']['users'][0]);
            $this->assertArrayHasKey('username', $data['data']['users'][0]);
            $this->assertArrayHasKey('role', $data['data']['users'][0]);
            $this->assertArrayHasKey('invite_status', $data['data']['users'][0]);
            $this->assertArrayHasKey('auto_invite', $data['data']['users'][0]);
            $this->assertArrayHasKey('email_status', $data['data']['users'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Listing member management negative */
    public function test_listing_negative()
    {
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['wrong_component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $response->assertStatus(404);
    }

    /**create member management positive */
    public function test_change_role_positive()
    {   
        $response = $this->post('/api/v1/manage/member-management/organization/change-role', $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }
    /**create member management positive */
    public function test_change_role_negative()
    {   
        $this->parameters['id']="";
        $response = $this->post('/api/v1/manage/member-management/organization/change-role', $this->parameters, $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**download positive */
    public function test_download_sample_positive()
    {   
        $response = $this->get('/api/v1/manage/member-management/download-sample?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**download negative */
    public function test_download_sample_negative()
    {   
        $response = $this->get('/api/v1/manage/member-management/download-sample?language='.$this->parameters['wrong_language'],$this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Delete member management positive */
    public function test_delete_positive()
    {
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language='.$this->parameters['language'],
            [
                'email'=> $this->parameters['invite_email'],
            ],
            $this->headers
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**Delete member management negative */
    public function test_delete_negative()
    {
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language='.$this->parameters['language'],
            [
                'id'=> $this->parameters['id'],
            ],
            $this->headers
        );
        $this->assertEquals(422, $response->getStatusCode());
    }
}
