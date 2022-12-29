<?php

namespace Tests\Feature\Master;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RanksTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_ranks()
    {
        $response = $this->get('/api/v1/master/ranks?language=en');
        $reponse=$response->json();
        $reponse=$response->json();
        if($reponse['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($reponse['success'],false);
        }

        //$response->assertStatus(200);
    }
}
