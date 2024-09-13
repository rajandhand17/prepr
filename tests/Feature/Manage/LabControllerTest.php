<?php

namespace Tests\Feature\Manage;

use App\Helpers\UtilityHelper;
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
            'language'               => 'en',
            'email'                  => 'schagpar@gmail.com',
            'password'               => 'Test@1234',
            'slug'                   => 'un-sdg-lab-1',
            'not_exists_slug'        => 'un-sdg-lab-2',
            'reference_id'           => '2',
            'duration_id'            => '1',
            'level_id'               => '1',
            'reference_type'         => 'lab',
            'like_component'         => 'like',
            'dislike_component'      => 'dislike',
            'title'                  => 'UN SDG Lab 1',
            'not_exist_title'        => 'UN SDG Lab 2',
            'organization_id'        => UtilityHelper::checkComponentSlugExistOrNot('organization', 'xyz-pvt-ltd')->uuid,
            'category_id'            => '1',
            'description'            => 'This lab is focused on driving awareness around the 17 UN sustainable development goals and to enable students and employees across the globe to co-lab and co-solve to create meaningful solutions.',
            'privacy'                => 'yes',
            'location'               => 'Ontario, Canada',
            'latitude'               => '51.2538',
            'longitude'              => '85.3232',
            'country'                => 'Canada',
            'city'                   => 'Ontario',
            'skills'                 => ['1', '2', '3'],
            'skill_groups.*'         => ['1', '2', '3'],
            'tags'                   => ['1', '2', '3'],
            'tag_groups'             => ['1', '2', '3'],
            // 'external_links'         => ['https://facebook.com', 'https://twiter.com'],
            // 'external_link_ids'      => ['1', '2', '3'],
            'request_type'           => 'publish',
            'type'                   => 'onboard',
            'is_notification_enabled'=> 'yes',
            'is_sequential'          => 'yes',
            'is_resource_sequential' => 'yes',
            'is_achievement_enabled' => 'no',
            'achievement_name'       => 'UN SDG Lab Completion Trophy',
            'achievement_points'     => ['1', '2', '3'],
            'lab_programs'           => ['1', '2', '3'],
            'challenges'             => ['1', '2'],
            'challenge_paths'        => ['1', '2'],
            'resource_modules'       => ['1', '2'],
            'resource_groups'        => ['1', '2'],
            'resource_collections'   => ['1', '2'],
            'achievement_conditions' => ['1', '2'],
        ];
        $this->baseUrl = '/api/v1/manage/';
        Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;

        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
    }

    public function test_lab_create_postive()
    {
        $response = $this->post('/api/v1/manage/lab/create', $this->parameters, $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_create_negative()
    {
        $response = $this->post('/api/v1/manage/lab/create', $this->parameters, $this->headers);
        $response->assertStatus(422);
    }

    public function test_lab_update_positive()
    {
        $this->parameters['_method'] = 'put';
        $response = $this->post('api/v1/manage/lab/'.$this->parameters['slug'].'/update', $this->parameters, $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_update_negative()
    {
        $response = $this->post('/api/v1/manage/lab/'.$this->parameters['slug'].'/update', $this->parameters, $this->headers);
        $response->assertStatus(405);
    }

    public function test_lab_view_positive()
    {
        $response = $this->get('/api/v1/manage/lab/'.$this->parameters['slug'].'?language=en', $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_view_negative()
    {
        $response = $this->get('/api/v1/manage/lab/'.$this->parameters['not_exists_slug'].'?language=en', $this->headers);
        $response->assertStatus(404);
    }

    public function test_lab_check_slug_postive()
    {
        $response = $this->get('/api/v1/manage/lab/check-slug/'.$this->parameters['not_exists_slug'].'?language=en', $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_check_slug_negative()
    {
        $response = $this->get('/api/v1/manage/lab/check-slug/'.$this->parameters['slug'].'?language=en', $this->headers);
        $response->assertStatus(400);
    }

    public function test_lab_check_title_positive()
    {
        $response = $this->get('/api/v1/manage/lab/check-title/'.$this->parameters['not_exist_title'].'?language=en', $this->headers);
        $response->assertStatus(200);
    }

    public function test_lab_check_title_negative()
    {
        $response = $this->get('/api/v1/manage/lab/'.$this->parameters['title'].'?language=en', $this->headers);
        $response->assertStatus(404);
    }
}
