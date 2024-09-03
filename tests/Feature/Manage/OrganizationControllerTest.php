<?php

namespace Tests\Feature\Manage;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class OrganizationControllerTest extends TestCase
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

        $this->parameters = [
            'language'              => 'en',
            'user_id'               => '10539',
            'title'                 => 'Amazon',
            'wrong_title'           => 'Accenture',
            'slug'                  => 'amazon',
            'wrong_slug'            => 'Accenture',
            'description'           => 'Describing the test cases of apis',
            'website'               => 'https://amazon.com',
            'about'                 => 'testing',
            'category'              => '2',
            'wrong_category'        => '199999',
            'status'                => 'publish',
            'status_wrong'          => 'wrong_status',
            'total_employees'       => '12',
            'latitude'              => '43.467517',
            'longitude'             => '-79.6876659',
            'address'               => 'Oakville, ON, Canada',
            'city'                  => 'Oakville',
            'state'                 => 'Ontario',
            'email'                 => 'rajan@amazon.com',
            'another_email'         => 'rajandhand17@gmail.com',
            'password'              => 'Prepr@123',
            'country'               => 'Canada',
            'search'                => 'Rforms',
            'wrong_search'          => 'wrong',
            'zip_code'              => 'L6M 3N5',
            'user_type'             => 'organization',
            'wrong_language'        => 'Hindi',
            'sort_by_ascending'     => 'name-a-to-z',
            'sort_by_descending'    => 'name-z-to-a',
            'sort_by_creation_date' => 'creation_date',
            'sort_by_wrong_input'   => 'default',
            'owner'                 => 'organization_owner',
            'organization_address'  => [
                [
                    'latitude' => '43.467517',
                    'longitude'=> '43.467517',
                    'address_1'=> 'Oakville',
                    'address_2'=> 'ON, Canada',
                    'city'     => 'Oakville',
                    'state'    => 'Ontario',
                    'country'  => 'Canada',
                    'zip_code' => 'L6M 3N6',
                ],
            ],
            'organization_members'=> [
                [
                    'name'    => 'Rajan',
                    'position'=> 'CEO',
                    // "image"=>null,
                ],

            ],
        ];
        Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
        $user = Auth::user();
        Auth::attempt(['email' =>$this->parameters['another_email'], 'password' =>$this->parameters['password']]);
        $userWithoutPermission = Auth::user();
        $this->token = $user->createToken(env('APP_NAME'))->accessToken;
        $this->tokenWithoutPermission = $userWithoutPermission->createToken(env('APP_NAME'))->accessToken;
        $this->headers = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->token,
        ];
        $this->headersWithoutPermission = [
            'Accept'        => 'application/json',
            'AUTHORIZATION' => 'Bearer '.$this->tokenWithoutPermission,
        ];
    }

    /**Create organization positive */
    public function test_create_organization_positive()
    {
        $response = $this->post('/api/v1/manage/organization/create', $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== []) {
                $this->assertArrayHasKey('title', $data['data']);
                $this->assertArrayHasKey('slug', $data['data']);
                $this->assertArrayHasKey('description', $data['data']);
                $this->assertArrayHasKey('website', $data['data']);
                $this->assertArrayHasKey('about', $data['data']);
                $this->assertArrayHasKey('status', $data['data']);
                $this->assertArrayHasKey('category', $data['data']);
                $this->assertArrayHasKey('lab_count', $data['data']);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Create organization negative*/
    public function test_create_organization_negative()
    {
        $response = $this->post('/api/v1/manage/organization/create', $this->parameters, $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** Get List organization positive */
    public function test_organization_view_get_list_positive()
    {
        $response = $this->get('/api/v1/manage/organization/get-list?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['data'] !== []) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** Get List organization negative */
    public function test_organization_view_get_list_negative()
    {
        $response = $this->get('/api/v1/manage/organization/get-list?language='.$this->parameters['wrong_language'], $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /** Get List with search positive*/
    public function test_organization_view_get_list_with_search_positive()
    {
        $response = $this->get('/api/v1/manage/organization/get-list?language='.$this->parameters['language'].'&search'.$this->parameters['search'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['data'] !== []) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** Get List with search negative*/
    public function test_organization_view_get_list_with_search_negative()
    {
        $response = $this->get('/api/v1/manage/organization/get-list?language='.$this->parameters['language'].'&search='.$this->parameters['wrong_search'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['data'] == []) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** View organization positive */
    public function test_organization_view_positive()
    {
        $response = $this->get('/api/v1/manage/organization/?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== []) {
                $this->assertArrayHasKey('id', $data['data']['list'][0]);
                $this->assertArrayHasKey('language', $data['data']['list'][0]);
                $this->assertArrayHasKey('title', $data['data']['list'][0]);
                $this->assertArrayHasKey('slug', $data['data']['list'][0]);
                $this->assertArrayHasKey('description', $data['data']['list'][0]);
                $this->assertArrayHasKey('cover_image', $data['data']['list'][0]);
                $this->assertArrayHasKey('profile_image', $data['data']['list'][0]);
                $this->assertArrayHasKey('website', $data['data']['list'][0]);
                $this->assertArrayHasKey('about', $data['data']['list'][0]);
                $this->assertArrayHasKey('category', $data['data']['list'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** View organization negative */
    public function test_organization_view_negative()
    {
        $response = $this->get('/api/v1/manage/organization/', $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /** View organization with search positive*/
    public function test_organization_view_positive_with_search_positive()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== []) {
                $this->assertArrayHasKey('id', $data['data']);
                $this->assertArrayHasKey('language', $data['data']);
                $this->assertArrayHasKey('title', $data['data']);
                $this->assertArrayHasKey('slug', $data['data']);
                $this->assertArrayHasKey('description', $data['data']);
                $this->assertArrayHasKey('cover_image', $data['data']);
                $this->assertArrayHasKey('profile_image', $data['data']);
                $this->assertArrayHasKey('website', $data['data']);
                $this->assertArrayHasKey('about', $data['data']);
                $this->assertArrayHasKey('category', $data['data']);
                $this->assertArrayHasKey('total_employees', $data['data']);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** View organization with search negative*/
    public function test_organization_view_positive_with_search_negative()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['wrong_slug'].'?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** fetech records without permission */
    public function test_organization_view_get_list_without_permission()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headersWithoutPermission);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** Organization check slug positive*/
    public function test_organization_check_slug_positive()
    {
        $response = $this->get('/api/v1/manage/organization/check-slug/'.$this->parameters['wrong_slug'].'?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** Organization check slug negative*/
    public function test_organization_check_slug_negative()
    {
        $response = $this->get('/api/v1/manage/organization/check-slug/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Organization  list positive */
    public function test_organization_list_positive()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== []) {
                $this->assertArrayHasKey('id', $data['data']);
                $this->assertArrayHasKey('language', $data['data']);
                $this->assertArrayHasKey('title', $data['data']);
                $this->assertArrayHasKey('slug', $data['data']);
                $this->assertArrayHasKey('description', $data['data']);
                $this->assertArrayHasKey('cover_image', $data['data']);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization  list negative */
    public function test_organization_list_negative()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['slug'], $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Organization  list  with search*/
    public function test_organization_list_with_search_positive()
    {
        $response = $this->get('/api/v1/manage/organization/?language='.$this->parameters['language'].'&search='.$this->parameters['title'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data']['list'] !== []) {
                $this->assertArrayHasKey('id', $data['data']['list'][0]);
                $this->assertArrayHasKey('language', $data['data']['list'][0]);
                $this->assertArrayHasKey('title', $data['data']['list'][0]);
                $this->assertArrayHasKey('slug', $data['data']['list'][0]);
                $this->assertArrayHasKey('description', $data['data']['list'][0]);
                $this->assertArrayHasKey('cover_image', $data['data']['list'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization  list positive with search*/
    public function test_organization_list_with_search_negative()
    {
        $response = $this->get('/api/v1/manage/organization/?language='.$this->parameters['language'].'&search='.$this->parameters['wrong_title'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data']['list'] == []) {
                $response->assertOk();
            } else {
                $this->fail();
            }
        } else {
            $this->fail();
        }
    }

    /**Organization get list with search status */
    public function test_organization_list_with_status_positive()
    {
        $response = $this->get('/api/v1/manage/organization/?language='.$this->parameters['language'].'&status='.$this->parameters['status'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data']['list'] !== []) {
                $this->assertArrayHasKey('id', $data['data']['list'][0]);
                $this->assertArrayHasKey('language', $data['data']['list'][0]);
                $this->assertArrayHasKey('title', $data['data']['list'][0]);
                $this->assertArrayHasKey('slug', $data['data']['list'][0]);
                $this->assertArrayHasKey('description', $data['data']['list'][0]);
                $this->assertArrayHasKey('cover_image', $data['data']['list'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization get list with search status */
    public function test_organization_list_with_status_negative()
    {
        $response = $this->get('/api/v1/manage/organization/?language='.$this->parameters['language'].'&status='.$this->parameters['status_wrong'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data']['list'] == []) {
                $response->assertOk();
            } else {
                $this->fail();
            }
        } else {
            $this->fail();
        }
    }

    /**Organization get listing based on category*/
    public function test_organization_list_with_category_positive()
    {
        $response = $this->get('/api/v1/manage/organization/?language='.$this->parameters['language'].'&category[]='.$this->parameters['category'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();

        if ($data['success']) {
            if ($data['data']['list'] !== []) {
                $this->assertArrayHasKey('id', $data['data']['list'][0]);
                $this->assertArrayHasKey('language', $data['data']['list'][0]);
                $this->assertArrayHasKey('title', $data['data']['list'][0]);
                $this->assertArrayHasKey('slug', $data['data']['list'][0]);
                $this->assertArrayHasKey('description', $data['data']['list'][0]);
                $this->assertArrayHasKey('cover_image', $data['data']['list'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization get listing based on category*/
    public function test_organization_list_with_category_negative()
    {
        $response = $this->get('/api/v1/manage/organization/?language='.$this->parameters['language'].'&category[]=['.$this->parameters['wrong_category'].']', $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data']['list'] == []) {
                $response->assertOk();
            } else {
                $this->fail();
            }
        } else {
            $this->fail();
        }
    }

    /**Organization get listing based on sorting*/
    public function test_organization_list_with_sort_by_ascending_positive()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'].'&sort_by='.$this->parameters['sort_by_ascending'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== []) {
                $this->assertArrayHasKey('id', $data['data']);
                $this->assertArrayHasKey('language', $data['data']);
                $this->assertArrayHasKey('title', $data['data']);
                $this->assertArrayHasKey('slug', $data['data']);
                $this->assertArrayHasKey('description', $data['data']);
                $this->assertArrayHasKey('cover_image', $data['data']);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization get listing based on sorting*/
    public function test_organization_list_with_sort_by_descending_positive()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'].'&sort_by='.$this->parameters['sort_by_descending'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== []) {
                $this->assertArrayHasKey('id', $data['data']);
                $this->assertArrayHasKey('language', $data['data']);
                $this->assertArrayHasKey('title', $data['data']);
                $this->assertArrayHasKey('slug', $data['data']);
                $this->assertArrayHasKey('description', $data['data']);
                $this->assertArrayHasKey('cover_image', $data['data']);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Organization get listing based on sorting*/
    public function test_organization_list_with_sort_by_creation_date_positive()
    {
        $response = $this->get('/api/v1/manage/organization/'.$this->parameters['slug'].'?language='.$this->parameters['language'].'&sort_by='.$this->parameters['sort_by_creation_date'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            if ($data['data'] !== []) {
                $this->assertArrayHasKey('id', $data['data']);
                $this->assertArrayHasKey('language', $data['data']);
                $this->assertArrayHasKey('title', $data['data']);
                $this->assertArrayHasKey('slug', $data['data']);
                $this->assertArrayHasKey('description', $data['data']);
                $this->assertArrayHasKey('cover_image', $data['data']);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Update the organization*/
    public function test_update_organization_positive()
    {
        $this->parameters['title'] = 'Amazon_update';
        $response = $this->put('/api/v1/manage/organization/'.$this->parameters['slug'].'/update', $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->json();
        if ($data['success']) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Update the organization*/
    public function test_update_organization_negative()
    {
        $this->parameters['title'] = '';
        $response = $this->put('/api/v1/manage/organization/'.$this->parameters['slug'].'/update', $this->parameters, $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Delete the organization*/
    public function test_organization_delete_positive()
    {
        $response = $this->delete('/api/v1/manage/organization/'.$this->parameters['slug'].'/delete', $this->parameters, $this->headers);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**Delete the organization*/
    public function test_organization_delete_negative()
    {
        $response = $this->delete('/api/v1/manage/organization/'.$this->parameters['slug'].'/delete', $this->parameters, $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }
}
