<?php

namespace Tests\Feature\Master;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabConditionsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/api/v1/master/lab-conditions?language=en');
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }
    }
}
