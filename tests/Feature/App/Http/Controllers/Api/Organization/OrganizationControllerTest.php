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
            $this->user_id = 4;
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

     }
    /**Organization create */
    public function test_create_organization_positive()
    {
        $response = $this->post('/api/v1/organization/create',['language'=> $this->language,"user_id"=>$this->user_id,"name"=>$this->name, "description"=> $this->description, "website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "latitude"=>$this->latitude,"longitude"=>$this->longitude,"address"=>$this->address,"city"=>$this->city,"state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();  
        if ($data['success']) {
                $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization create negative*/
    public function test_create_organization_negative()
    {
        $response = $this->post('/api/v1/organization/create',['language'=> $this->language,"user_id"=>$this->user_id,"name"=>$this->name, "description"=> $this->description, "website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "latitude"=>$this->latitude,"longitude"=>$this->longitude,"address"=>$this->address,"city"=>$this->city,"state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Organization Update */
    public function test_update_organization_positive()
    {
        $response = $this->post('/api/v1/organization/update',['language'=> $this->language,"slug"=>$this->slug,"user_id"=>$this->user_id,"website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code]);
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
        $response = $this->post('/api/v1/organization/update',['language'=> $this->language,"user_id"=>$this->user_id,"website"=>$this->website, "about"=>$this->about, "category"=>$this->category, "status"=>$this->status, "total_employees"=>$this->total_employees, "state"=>$this->state,"country"=>$this->country,"zip_code"=>$this->zip_code]);
        $this->assertEquals(422, $response->getStatusCode());
        
    }
    
    /**Organization Listing */
    public function test_organization_list()
    {
        $response = $this->get('/api/v1/organization/list?search=prepr&language=en');
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

    /**Organization Listing negative*/
    public function test_organization_list_negative()
    {
        $response = $this->get('/api/v1/organization/list');
        $this->assertEquals(400, $response->getStatusCode());
       
    }

     /** Organization view */
    public function test_organization_view_positive()
    {
        $response = $this->get('/api/v1/organization/view?language='.$this->language.'&slug='.$this->slug);
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
        $response = $this->get('/api/v1/organization/view?language=en');
        $this->assertEquals(422, $response->getStatusCode());
        
    }

    /**Organization Delete */
    public function test_delete_organization_postive()
    {
        $response = $this->post('/api/v1/organization/delete?language='.$this->language.'&slug='.$this->slug);
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
        $response = $this->post('/api/v1/organization/delete?language='.$this->language);
        $this->assertEquals(422, $response->getStatusCode());
        
    }
}
