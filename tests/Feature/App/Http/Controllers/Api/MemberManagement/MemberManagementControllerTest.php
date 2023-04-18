<?php

namespace Tests\Feature\App\Http\Controllers\Api\MemberManagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberManagementControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function setUp(): void
    {
           parent::setUp();
           $this->parameters= [
                'language'=> 'en',
                'user_id' =>'2',
                'name' =>'Prepr',
                'slug' =>'preprtestsd',
                "wrong_language"=>"Hindi",
                "page"=>"1",
                "component"=>"organisation",
                "type"=>"email",
                "invite_type"=>"email",
                "role"=>"orgnization_manager",
                "module_id"=>"26",
                "subject_line"=>"testing",
                "email_body"=>"email messages",
                "invite_status"=>"pending",
                "user_invite_email"=>"rajan@prepr.org",
                "inviter_id"=>"15",
                "id"=>["1"],
            ];

   }
   /**create member management positive */
   public function test_create_positive(){
    $response = $this->post('/api/v1/member-management/organization/prepr/create?language=en',$this->parameters);
    $this->assertEquals(200, $response->getStatusCode());
   }
   
   
   /**create member management negative */
   public function test_create_negative(){
            $response = $this->post('/api/v1/member-management/organization/prepr/create?language=en',[]);
            $this->assertEquals(422, $response->getStatusCode());
   }


   /**Listing member management positive */
   public function test_listing_positive()
   {   
       $response = $this->get('/api/v1/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'].'&page='.$this->parameters['page']);
       $response->assertStatus(200);
       $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('module_id', $data['data'][0]);
            $this->assertArrayHasKey('invite_status', $data['data'][0]);
            $this->assertArrayHasKey('email', $data['data'][0]);
            $this->assertArrayHasKey('email_status', $data['data'][0]);
            $this->assertArrayHasKey('email_resend_status', $data['data'][0]);
            $this->assertArrayHasKey('is_exist', $data['data'][0]);
            $this->assertArrayHasKey('is_evaluator', $data['data'][0]);
            $this->assertArrayHasKey('is_join_request', $data['data'][0]);
            $this->assertArrayHasKey('join_request_status', $data['data'][0]);
            $this->assertArrayHasKey('auto_invite_status', $data['data'][0]);
            $this->assertArrayHasKey('user_status', $data['data'][0]);
            $this->assertArrayHasKey('user_name', $data['data'][0]);
            $this->assertArrayHasKey('user_profile_image', $data['data'][0]);
            $this->assertArrayHasKey('type', $data['data'][0]);
            $this->assertArrayHasKey('invite_type', $data['data'][0]);
            $this->assertArrayHasKey('module_type', $data['data'][0]);
        }
   }
  
   
   /**Listing member management negative */
   public function test_listing_negative()
   {   
       $response = $this->get('/api/v1/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug']);
       $response->assertStatus(400);
   }
   
   
   /**Delete member management positive */
   public function test_delete_positive(){
    $response = $this->post('/api/v1/member-management/organization/prepr/delete?language=en',
    [
    "id"=>$this->parameters['id'],
    ]);
    $this->assertEquals(200, $response->getStatusCode());
    }

   
   /**Delete member management negative */
    public function test_delete_negative(){
        $response = $this->post('/api/v1/member-management/organization/prepr/delete?language=en',
        [
        "id"=>$this->parameters['id'],
        ]);
        $this->assertEquals(500, $response->getStatusCode());
    }

}