<?php

namespace Tests\Feature\App\Http\Controllers\Api\Organization;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class OrganizationControllerTest extends TestCase
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
                'slug' =>'Prepr',
                'description'=>"Describing the test cases of apis",
                'website'=>"prepr.org",
                'about'=>"testing",
                "category"=>"2",
                "status"=>"1",
                "total_employees"=>"12",
                "latitude"=>"43.467517",
                "longitude"=>"-79.6876659",
                "address"=>"Oakville, ON, Canada",
                "city"=>"Oakville",
                "state"=>"Ontario",
                "country"=>"Canada",
                "zip_code"=>"L6M 3N5",
                "user_type"=>"organization",
                
                ];
            $data=Auth::attempt(['email' =>"rajan@prepr.orgs", 'password' =>"Prepr@123"]);
            $user = Auth::user(); 
            $this->token=$user->createToken(env("APP_NAME"))->accessToken;
            $this->headers = [
                'Accept'        => 'application/vnd.laravel.v1+json',
                'AUTHORIZATION' => 'Bearer '.$this->token,
                ];
     }
    /**Organization create */
    public function test_create_organization_positive()
    {   
          $response = $this->post('/api/v1/organization/create',$this->parameters, $this->headers);
          $this->assertEquals(200, $response->getStatusCode());
          $data = $response->json();  
            if ($data['success']){
                $this->assertArrayHasKey('name', $data['data']);
                $this->assertArrayHasKey('slug', $data['data']);
                $response->assertOk();
                    
            } else {
                $this->fail();
            }
    }
    
    /**Organization create */
    public function test_create_organization_negative_with_header()
    {   
          $response = $this->post('/api/v1/organization/create',$this->parameters, $this->headers);
            $this->assertEquals(422, $response->getStatusCode());
            
    }
    /**Organization create negative*/
    public function test_create_organization_negative_without_header()
    {  
        $response = $this->post('/api/v1/organization/create',$this->parameters);
         $this->assertEquals(500, $response->getStatusCode());
    }

    /**Organization Listing */
    public function test_organization_list()
    {   
        $response = $this->get('/api/v1/organization/?language='.$this->parameters['language'].'',$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('language', $data['data'][0]);
            $this->assertArrayHasKey('display_name', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('slug', $data['data'][0]);
            $this->assertArrayHasKey('description', $data['data'][0]);
            $this->assertArrayHasKey('cover_image', $data['data'][0]);
            $this->assertArrayHasKey('profile_image', $data['data'][0]);
            $this->assertArrayHasKey('website', $data['data'][0]);
            $this->assertArrayHasKey('about', $data['data'][0]);
            $this->assertArrayHasKey('category', $data['data'][0]);
            $this->assertArrayHasKey('status', $data['data'][0]);
            $this->assertArrayHasKey('total_employees', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }
    
    /**Organization Update */
    public function test_update_organization_positive()
    {     
        $this->parameters['name']="Prepr_testcase";
        $response = $this->post('/api/v1/organization/update',['language'=> $this->parameters['language'],"slug"=>$this->parameters['slug'],"zip_code"=>$this->parameters['zip_code']], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
            $data = $response->json(); 
            if ($data['success']) {
                    $response->assertOk();
            } else {
                $this->fail();
            }
    }

    
    /**Organization Update negative*/
    public function test_update_organization_negative()
    {   
        $response = $this->post('/api/v1/organization/update',$this->parameters,$this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }
    
    /**Organization Listing negative*/
    public function test_organization_list_negative()
    {   
        $response = $this->get('/api/v1/organization/',$this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

     /** Organization view */
    public function test_organization_view_positive()
    {   
        $response = $this->get('/api/v1/organization/view?language='.$this->parameters['language'].'&slug='.$this->parameters['slug'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if($data['success']) {
            $this->assertArrayHasKey('id', $data['data'][0]);
            $this->assertArrayHasKey('language', $data['data'][0]);
            $this->assertArrayHasKey('name', $data['data'][0]);
            $this->assertArrayHasKey('slug', $data['data'][0]);
            $this->assertArrayHasKey('description', $data['data'][0]);
            $this->assertArrayHasKey('cover_image', $data['data'][0]);
            $this->assertArrayHasKey('profile_image', $data['data'][0]);
            $this->assertArrayHasKey('website', $data['data'][0]);
            $this->assertArrayHasKey('about', $data['data'][0]);
            $this->assertArrayHasKey('category', $data['data'][0]);
            $this->assertArrayHasKey('status', $data['data'][0]);
            $this->assertArrayHasKey('total_employees', $data['data'][0]);
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** Organization view */
    public function test_organization_view_negative()
    {    
        $response = $this->get('/api/v1/organization/view?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(422, $response->getStatusCode());
        
    }

    /**Organization Delete */
    public function test_delete_organization_postive()
    {   
        $response = $this->post('/api/v1/organization/delete', ["slug" => $this->parameters['slug'], "language" => $this->parameters['language']],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
                $response->assertOk();
        }else {
            $this->fail();
        }
    }

    
    /**Organization Delete negative*/
    public function test_delete_organization_negative()
    {   
        $response = $this->post('/api/v1/organization/delete', ["language" => $this->parameters['language']],$this->headers);
        $this->assertEquals(422, $response->getStatusCode());
        
    }
}
