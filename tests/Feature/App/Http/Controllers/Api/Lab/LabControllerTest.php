<?php

namespace Tests\Feature\App\Http\Controllers\Api\Lab;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LabControllerTest extends TestCase
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
            'slug'          =>'un-sdg-lab',
            'not_exists_slug'=>'un-sdg-labs',
            'reference_id' =>'2',
            'reference_type' =>'lab',
            'is_like' =>'like',
        ];
        $data=Auth::attempt(['email' =>'rajan@prepr.orgs', 'password' =>'Prepr@123']);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;
        $this->headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }

    public function test_lab_list()
    {
        $response = $this->get('/api/v1/lab?language=en',$this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_view()
    {
        $response = $this->get('/api/v1/lab/'.$this->parameters['slug'].'?language=en',$this->headers);
        $response->assertStatus(200);
    }
    public function test_lab_check_slug()
    {
        $response = $this->get('/api/v1/lab/check-slug/'.$this->parameters['not_exists_slug'].'?language=en',$this->headers);
        $response->assertStatus(200);
    }
    public function test_lab_check_title()
    {
        $response = $this->get('/api/v1/lab/check-title/'.$this->parameters['slug'].'?language=en',$this->headers);
        $response->assertStatus(200);
    }
    public function test_like_unlike(){
        $response=$this->post('/api/v1/lab/like/like-unlike',$this->parameters,$this->headers);
        dd($response);
        $response->assertStatus(200);
    }
}
