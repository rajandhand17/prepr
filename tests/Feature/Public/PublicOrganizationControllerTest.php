<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PublicOrganizationControllerTest extends TestCase
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

    public function test_organization_list_positive()
    {
        $response = $this->get('/api/v1/public/organization?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']){
            $this->assertArrayHasKey('id', $data['data'][0]['list'][0]);
            $this->assertArrayHasKey('language',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('title',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('slug',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('description',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('cover_image',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('profile_image',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('website',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('about',$data['data'][0]['list'][0]);
            $this->assertArrayHasKey('category',$data['data'][0]['list'][0]);
            $response->assertOk();
        }else {
            $this->fail();
        }
    }

    public function test_organization_list_negative()
    {
        $response = $this->get('/api/v1/public/organization?language='.$this->parameters['wrong_language'],$this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public  function test_organization_view_positive() {
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']){
            $this->assertArrayHasKey('id', $data['data']);
            $this->assertArrayHasKey('language',$data['data']);
            $this->assertArrayHasKey('title',$data['data']);
            $this->assertArrayHasKey('slug',$data['data']);
            $this->assertArrayHasKey('description',$data['data']);
            $this->assertArrayHasKey('cover_image',$data['data']);
            $this->assertArrayHasKey('profile_image',$data['data']);
            $this->assertArrayHasKey('website',$data['data']);
            $this->assertArrayHasKey('about',$data['data']);
            $this->assertArrayHasKey('category',$data['data']);
            $response->assertOk();
        }else {
            $this->fail();
        }
    }

    public  function test_organization_view_negative() {
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['wrong_slug'].'?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public  function test_organization_view_with_search_positive() {
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'].'&search='.$this->parameters['slug'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']){
            $this->assertArrayHasKey('id', $data['data']);
            $this->assertArrayHasKey('language',$data['data']);
            $this->assertArrayHasKey('title',$data['data']);
            $this->assertArrayHasKey('slug',$data['data']);
            $this->assertArrayHasKey('description',$data['data']);
            $this->assertArrayHasKey('cover_image',$data['data']);
            $this->assertArrayHasKey('profile_image',$data['data']);
            $this->assertArrayHasKey('website',$data['data']);
            $this->assertArrayHasKey('about',$data['data']);
            $this->assertArrayHasKey('category',$data['data']);
            $response->assertOk();
        }else {
            $this->fail();
        }
    }

    public  function test_organization_view_with_search_negative() {
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['wrong_slug'].'?language='.$this->parameters['language'].'&search='.$this->parameters['slug'],$this->headers);
        $this->assertEquals(404, $response->getStatusCode());

    }
    public  function  test_follow_organization_positive(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['slug'].'/follow?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public  function  test_follow_organization_negative(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['wrong_slug'].'/follow?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public  function  test_un_follow_organization_positive(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['slug'].'/un-follow?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public  function  test_un_follow_organization_negative(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['wrong_slug'].'/un-follow?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public  function  test_like_organization_positive(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['slug'].'/like?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }
    public  function  test_like_organization_negative(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['wrong_slug'].'/like?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }
    public  function  test_un_like_organization_positive(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['slug'].'/un-like?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }
    public  function  test_un_like_organization_negative(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['wrong_slug'].'/un-like?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }
    public  function  test_share_organization_positive(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['slug'].'/share?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }
    public  function  test_share_organization_negative(){
        $response = $this->get('/api/v1/public/organization/'.$this->parameters['wrong_slug'].'/share?language='.$this->parameters['language'],$this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
