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
            'title'            => 'UN SDG Lab 1',
            'category'         => '1',
            'organization_id'  => '582',
            'organization_id_not_exists'  => '5821',
            'slug'             => 'un-sdg-lab-1',
            'wrong_slug'       => 'wrong_slug',
            'email'            => 'schagpar@gmail.com',
            'password'         => 'Test@1234',
            'type'             => 'join_request',
            'invite_type'      => 'join_request',
            'subject_line'     => 'Successfully Joined Lab',
            'email_body'       => 'You have successfully joined Lab',
            'auto_invite'      => 'yes',
            'sort_by_name_z_to_a'          => 'name-z-to-a',
            'sort_by_name_a_to_z'          => 'name-a-to-z',
            'social_type_liked'=> 'liked',
            'social_type_liked_wrong'=> 'like',
            'social_type_favourites'=> 'favourites',
            'social_type_favourites_wrong'=> 'favourit',
            'skills'         => '1',
            'wrong_skills'   => '111',
            'privacy_private'=> 'private',
            'privacy_public' => 'privacy_public',
            'wrong_privacy'  => 'not_exists',
            'tags'           => '1',
            'wrong_tags'     => '111',

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
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }
    public function test_lab_list_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['wrong_language'], $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_lab_list_filter_search_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&search='.$this->parameters['title'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_search_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&search='.$this->parameters['title'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_category_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&category[]='.$this->parameters['category'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_category_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&category[]='.$this->parameters['category'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_organization_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&organization_id[]='.$this->parameters['organization_id'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_organization_id_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&organization_id[]='.$this->parameters['organization_id_not_exists'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_sort_by_name_z_to_a_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&sort_by[]='.$this->parameters['sort_by_name_z_to_a'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_sort_by_name_z_to_a_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&sort_by[]='.$this->parameters['sort_by_name_a_to_z'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_social_type_like_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&social_type='.$this->parameters['social_type_liked'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_social_type_like_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&social_type[]='.$this->parameters['social_type_liked_wrong'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_social_type_favourite_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&social_type='.$this->parameters['social_type_favourites'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_social_type_favourite_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&social_type[]='.$this->parameters['social_type_favourites_wrong'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_skills_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&skills[]='.$this->parameters['skills'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_skills_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&skills[]='.$this->parameters['wrong_skills'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_privacy_private_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&privacy='.$this->parameters['privacy_private'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_privacy_private_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&privacy='.$this->parameters['wrong_privacy'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_privacy_public_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&privacy='.$this->parameters['privacy_public'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_privacy_public_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&privacy='.$this->parameters['wrong_privacy'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
    }

    public function test_lab_list_filter_tags_positive()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&&tags[]='.$this->parameters['tags'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('id', $data['data']['list'][0]);
            $this->assertArrayHasKey('language', $data['data']['list'][0]);
            $this->assertArrayHasKey('title', $data['data']['list'][0]);
            $this->assertArrayHasKey('slug', $data['data']['list'][0]);
            $this->assertArrayHasKey('description', $data['data']['list'][0]);
            $this->assertArrayHasKey('privacy', $data['data']['list'][0]);
            $this->assertArrayHasKey('media_type', $data['data']['list'][0]);
            $this->assertArrayHasKey('media', $data['data']['list'][0]);
            $this->assertArrayHasKey('category', $data['data']['list'][0]);
            $this->assertArrayHasKey('status', $data['data']['list'][0]);
            $this->assertArrayHasKey('member_count', $data['data']['list'][0]);
            $this->assertArrayHasKey('likes', $data['data']['list'][0]);
            $this->assertArrayHasKey('shares', $data['data']['list'][0]);
            $this->assertArrayHasKey('joined', $data['data']['list'][0]);
            $this->assertArrayHasKey('liked', $data['data']['list'][0]);
            $this->assertArrayHasKey('favourite', $data['data']['list'][0]);
        }
    }

    public function test_lab_list_filter_tags_negative()
    {
        $response = $this->get('/api/v1/public/lab?language='.$this->parameters['language'].'&tags[]='.$this->parameters['wrong_tags'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $this->assertArrayHasKey('total_count', $data['data']);
            $this->assertArrayHasKey('per_page', $data['data']);
            $this->assertArrayHasKey('count', $data['data']);
            $this->assertArrayHasKey('current_page', $data['data']);
            $this->assertArrayHasKey('total_pages', $data['data']);
            $this->assertArrayHasKey('list', $data['data']);

        }
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
            $this->assertArrayHasKey('media_type', $data['data']);
            $this->assertArrayHasKey('media', $data['data']);
            $this->assertArrayHasKey('category', $data['data']);
            $this->assertArrayHasKey('status', $data['data']);
            $this->assertArrayHasKey('member_count', $data['data']);
            $this->assertArrayHasKey('likes', $data['data']);
            $this->assertArrayHasKey('shares', $data['data']);
            $this->assertArrayHasKey('joined', $data['data']);
            $this->assertArrayHasKey('liked', $data['data']);
        }
    }

    public function test_lab_view_negative()
    {
        $response = $this->get('/api/v1/public/lab/'.$this->parameters['wrong_slug'].'?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $this->assertEquals(404, $response->getStatusCode());
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
}
