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
    
    /**Organization create */
    public function test_create_organization()
    {
        $response = $this->post('/api/v1/organization/create-organization',['language'=>"en","user_id"=>5,"name"=>"prepr", "description"=>"description", "website"=>"prepr.org", "about"=>"testing the test", "category"=>"2", "status"=>"1", "total_employees"=>"12"]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
                $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization Update */
    public function test_update_organization()
    {
        $response = $this->post('/api/v1/organization/update-organization',['language'=>"en", "status"=>"1", "total_employees"=>"12","organization_id"=>"1"]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
                $response->assertOk();
        } else {
            $this->fail();
        }
    }
    
    /**Organization Listing */
    public function test_organization_list()
    {
        $response = $this->get('/api/v1/organization/organization-list?search=prepr&language=en');
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
    /**Organization Delete */
    public function test_delete_organization()
    {
        $response = $this->post('/api/v1/organization/delete-organization',['language'=>"en","organization_id"=>"1"]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
                $response->assertOk();
        } else {
            $this->fail();
        }
    }

     /** Organization view */
    public function test_organization_view()
    {
        $response = $this->post('/api/v1/organization/view-organization',['slug'=>'prepr','language'=>'en']);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
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


   
}
