<?php

namespace Tests\Feature\Manage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabProgramControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function setUp(): void
    {
        parent::setUp();
        $this->parameters = [
            'language'               => 'en',
            'email'                  => 'rajan@amazon.com',
            'password'               => 'Prepr@123',
            'slug'                   => 'amazon-lab',
            'not_exists_slug'        => 'un-sdg-labs',
            'title'                  =>'Creating the Lab Programs',
            'wrong_title'            =>'Creating the Lab Programs with another title',
        ];
    }
    public function test_check_title_positive(): void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-slug/'.$this->parameters['wrong_title'].'/');
        $response->assertStatus(200);
    }

    public function test_check_title_negative():void
    {
        $response = $this->get('/api/v1/manage/lab-program/check-slug/'.$this->parameters['wrong_title'].'/');
        $response->assertStatus(403);
    }
}
