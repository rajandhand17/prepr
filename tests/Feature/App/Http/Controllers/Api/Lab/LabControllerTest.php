<?php

namespace Tests\Feature\App\Http\Controllers\Api\Lab;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
