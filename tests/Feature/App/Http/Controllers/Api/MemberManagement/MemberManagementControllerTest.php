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
           $this->language="en";
           $this->wrong_language="hindi";
           $this->id=3;
           $this->ids='{ "language":"en", "slug":"3,4"}';
   }
   public function test_listing_positive()
   {   
       $response = $this->get('/api/v1/member-management/'.$this->language.'/');
       $response->assertStatus(200);
   }
   public function test_listing_negative()
   {   
       $response = $this->get('/api/v1/member-management/'.$this->wrong_language.'/');
       $response->assertStatus(400);
   }

   public function test_delete_positive()
   {   
       $response = $this->get('api/v1/member-management/3/delete?language=en');
       $response->assertStatus(200);
   }

   public function test_delete_negative()
   {   
       $response = $this->delete('api/v1/member-management/'.$this->id.'/delete');
       $response->assertStatus(400);
   }

   public function test_delete_multiple_positive()
   {   
       $response = $this->post('api/v1/member-management/delete',$this->ids);
       $response->assertStatus(200);
   }

}
