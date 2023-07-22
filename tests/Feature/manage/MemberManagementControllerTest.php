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
            'name'           => 'Infosyes',
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
            'email'          =>'rajan@amazon.com',
            'password'       =>'Prepr@123',
            'invite_status'  => 'pending',
            'invite_email'   => ['rajan@prepr.org'],
            'inviter_id'     => '15',
            'id'             => ['1'],
            'auto_invite'    => 'yes',
        ];
        $data = Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;

        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }

    /**create member management positive */
    public function test_create_positive()
    {   
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/create?language=en', $this->parameters,$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**create member management negative */
    public function test_create_negative()
    {
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/create?language=en', [], $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Listing member management positive */
    public function test_listing_positive()
    {
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'?language=en', $this->headers);

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
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['wrong_component'].'/'.$this->parameters['slug'].'?language=en', $this->headers);

        $response->assertStatus(404);
    }

    /**Delete member management positive */
    public function test_delete_positive()
    {
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language=en',
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
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language=en',
            [
                'id'=> $this->parameters['id'],
            ],
            $this->headers
        );
        $this->assertEquals(422, $response->getStatusCode());
    }
}
