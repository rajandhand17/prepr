<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PublicLabControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->parameters = [
            'language'       => 'en',
            'wrong_language'  => 'hi',
            'slug'           => 'prepr',
            'wrong_slug'     => 'wrong_slug',
            'email'          =>'rajan@amazon.com',
            'password'       =>'Prepr@123',
        ];
        Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;
        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }
    public function test_lab_list_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']){
            $this->assertArrayHasKey('id', $data['data']['data'][0]);
            $this->assertArrayHasKey('language',$data['data']['data'][0]);
            $this->assertArrayHasKey('title',$data['data']['data'][0]);
            $this->assertArrayHasKey('slug',$data['data']['data'][0]);
            $this->assertArrayHasKey('description',$data['data']['data'][0]);
        }
    }

    public function test_lab_list_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['wrong_language'],$this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_lab_view_positive()
    {
        $response = $this->get('/api/v1/public/lab/un-sdg-lab-1?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();

        if ($data['success']){
            $this->assertArrayHasKey('id', $data['data']);
            $this->assertArrayHasKey('language',$data['data']);
            $this->assertArrayHasKey('title',$data['data']);
            $this->assertArrayHasKey('slug',$data['data']);
            $this->assertArrayHasKey('description',$data['data']);
            $this->assertArrayHasKey('privacy',$data['data']);
            $this->assertArrayHasKey('status',$data['data']);
        }
    }

}
