<?php

namespace Tests\Feature\Master;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FlexibleDateDurationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/api/v1/master/flexible-date-duration?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertStatus(200);
        }else{
            $this->assertEquals($reponse['success'],false);
        }
       
    }
}
