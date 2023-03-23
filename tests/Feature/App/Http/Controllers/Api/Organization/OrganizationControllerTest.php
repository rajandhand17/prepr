<?php

namespace Tests\Feature\App\Http\Controllers\Api\Organization;

use Tests\TestCase;

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
            $this->language = "en";
            $this->user_id = 7;
            $this->name="Prepr";
            $this->slug="prepr";
            $this->description="Describing the test cases of apis";
            $this->website="prepr.org";
            $this->about="testing";
            $this->category="2";
            $this->status="1";
            $this->total_employees="12";
            $this->latitude="43.467517";
            $this->longitude="-79.6876659";
            $this->address="Oakville, ON, Canada";
            $this->city="Oakville";
            $this->state="Ontario";
            $this->country="Canada";
            $this->zip_code="L6M 3N5";
            $this->user_type="organization";
            $this->email = "rajan@prepr.orgs";
            $this->password = "Prepr@123";
            $this->token="";
     }
    /**Organization create */
    public function test_create_organization_positive()
    {   
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
          $response = $this->post('/api/v1/organization/create',['language'=> $this->language,"user_id"=>$this->user_id,"name"=>$this->name, "description"=> $this->description, "website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "latitude"=>$this->latitude,"longitude"=>$this->longitude,"address"=>$this->address,"city"=>$this->city,"state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code], $headers);
       
            $this->assertEquals(200, $response->getStatusCode());
            $data = $response->json();  
            if ($data['success']) {
                    $response->assertOk();
            } else {
                $this->fail();
            }
           
       
    }
    
    /**Organization create */
    public function test_create_organization_negative_with_header()
    {   
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
          $response = $this->post('/api/v1/organization/create',['language'=> $this->language,"user_id"=>$this->user_id,"name"=>$this->name, "description"=> $this->description, "website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "latitude"=>$this->latitude,"longitude"=>$this->longitude,"address"=>$this->address,"city"=>$this->city,"state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code], $headers);
            $this->assertEquals(422, $response->getStatusCode());
            
    }
    /**Organization create negative*/
    public function test_create_organization_negative()
    {  
            $response = $this->post('/api/v1/organization/create',['language'=> $this->language,"user_id"=>$this->user_id,"name"=>$this->name, "description"=> $this->description, "website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "latitude"=>$this->latitude,"longitude"=>$this->longitude,"address"=>$this->address,"city"=>$this->city,"state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code]);
        
            $this->assertEquals(500, $response->getStatusCode());
    }

    /**Organization Listing */
    public function test_organization_list()
    {   
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
        $response = $this->get('/api/v1/organization/?search='.$this->name.'&language=en',$headers);
     
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
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
          $response = $this->post('/api/v1/organization/update',['language'=> $this->language,"slug"=>$this->slug, "description"=> $this->description,"zip_code"=>$this->zip_code], $headers);
         
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
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
        $response = $this->post('/api/v1/organization/update',['language'=> $this->language,"user_id"=>$this->user_id,"website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code],$headers);
        $this->assertEquals(422, $response->getStatusCode());
        
    }
    
    /**Organization Listing negative*/
    public function test_organization_list_negative()
    {   
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
        $response = $this->get('/api/v1/organization/',$headers);
        $this->assertEquals(400, $response->getStatusCode());
       
    }

     /** Organization view */
    public function test_organization_view_positive()
    {   
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
        $response = $this->get('/api/v1/organization/view?language='.$this->language.'&slug='.$this->slug,$headers);
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
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
        $response = $this->get('/api/v1/organization/view?language=en',$headers);
        $this->assertEquals(422, $response->getStatusCode());
        
    }

    /**Organization Delete */
    public function test_delete_organization_postive()
    {   
        
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
        $response = $this->post('/api/v1/organization/delete', ["slug" => $this->slug, "language" => $this->language],$headers);
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
        
        $response = $this->post('/api/v1/auth/login', ["email" => $this->email, "password" => $this->password, "language" => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true){
            if(isset($data['data']['token'])){
                $this->token=$data['data']['token'];
                $response->assertOk();
            }else{
            $userrecords=User::select("otp")->where("email",$this->email)->first();
            $twofactorresponse= $this->post('/api/v1/auth/verify-two-factor', ["email" => $this->email,"otp" => $userrecords->otp, "language" => $this->language]);
            $twofactorresponse->assertStatus(200);
            $this->token=$twofactorresponse['data']['token'];
           }
        }else {
            $this->fail();
        }  
        $headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
            ];
        $response = $this->post('/api/v1/organization/delete', ["language" => $this->language],$headers);
        $this->assertEquals(422, $response->getStatusCode());
        
    }
}
