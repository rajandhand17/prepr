<?php

namespace Tests\Feature\App\Http\Controllers\Api\Lab;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LabControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected $parameters;

    public function setUp(): void
    {
        parent::setUp();
        $data = Auth::attempt(['email' =>'vinod@prepr.org', 'password' =>'Test@1234']);
        $user = Auth::user();

        $this->token = $user->createToken(env('APP_NAME'))->accessToken;
        $this->headers = [
            'Accept'        => 'application/vnd.laravel.v1+json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }

    public function test_lab_list_positive()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
