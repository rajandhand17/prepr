<?php

namespace Tests\Feature\Public;

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
            'language'         => 'en',
            'wrong_language'   => 'hi',
            'slug'             => 'un-sdg-lab-1',
            'wrong_slug'       => 'wrong_slug',
            'email'            => 'schagpar@gmail.com',
            'password'         => 'Test@1234',
            'type'             => 'join_request',
            'invite_type'      => 'join_request',
            'subject_line'     => 'Successfully Joined Lab',
            'email_body'       => 'You have successfully joined Lab',
            'auto_invite'      => 'yes',

        ];
        Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;
        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }

    public function test_like_lab_public_lab_positive()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/like?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_like_lab_public_lab_negative()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/like?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_un_like_lab_public_lab_positive()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/un-like?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_un_like_lab_public_lab_negative()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/un-like?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_favorite_organization_positive()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/favourite?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_favorite_organization_negative()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['wrong_slug'].'/favourite?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_un_favorite_organization_positive()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/un-favourite?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_un_favorite_organization_negative()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['wrong_slug'].'/un-favourite?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_share_positive()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/share?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_share_negative()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/share?language='.$this->parameters['wrong_language'], $this->parameters, $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_join_positive()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/join?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_join_negative()
    {
        $response = $this->post('/api/v1/public/lab/'.$this->parameters['slug'].'/join?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_un_join_positive()
    {
        $response = $this->delete('/api/v1/public/lab/'.$this->parameters['slug'].'/un-join?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_un_join_negative()
    {
        $response = $this->delete('/api/v1/public/lab/'.$this->parameters['slug'].'/un-join?language='.$this->parameters['language'], $this->parameters, $this->headers);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_lab_list_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['wrong_language'], $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_lab_view_positive()
    {
        $response = $this->get('/api/v1/public/lab/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();

        if ($data['success']) {
            $this->assertArrayHasKey('id', $data['data']);
            $this->assertArrayHasKey('language', $data['data']);
            $this->assertArrayHasKey('title', $data['data']);
            $this->assertArrayHasKey('slug', $data['data']);
            $this->assertArrayHasKey('description', $data['data']);
            $this->assertArrayHasKey('privacy', $data['data']);
            $this->assertArrayHasKey('status', $data['data']);
        }
    }

    public function test_lab_view_negative()
    {
        $response = $this->get('/api/v1/public/lab/'.$this->parameters['wrong_slug'].'?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
