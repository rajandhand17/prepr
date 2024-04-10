<?php

namespace Tests\Feature\Manage;

use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MemberManagementControllerTest extends TestCase
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
            'title'                 => 'Accenture',
            'type'                  => 'invite',
            'auto_invite'           => 'yes',
            'role'                  => 'Organization Manager',
            'invite_type'           => 'email',
            'wrong_title'           => 'Infosys',
            'invite_email'          => ['rajan@prepr.org', 'shagun@gmail.com'],
            'slug'                  => 'rform',
            'component'             => 'organization',
            'wrong_component'       => 'component',
            'wrong_slug'            => 'infosys',
            'description'           => 'Describing the test cases of apis',
            'website'               => 'https://infosys.com',
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
                ],

            ],

        ];
        $data = Auth::attempt(['email' =>$this->parameters['email'], 'password' =>$this->parameters['password']]);
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
        global $id;
    }

    /**create member management positive */

    public function test_add_member_to_organization_positive()
    {
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/create?language='.$this->parameters['language'], $this->parameters, $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['data'] !== null) {
            $this->assertArrayHasKey('invalid_emails', $data['data']);
            $this->assertArrayHasKey('invited_emails', $data['data']);
            $this->assertArrayHasKey('already_members', $data['data']);
        }
        $response->assertOk();
    }

    /**create member management negative */
    public function test_add_member_to_organization_negative()
    {
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['wrong_slug'].'/create?language='.$this->parameters['language'], [], $this->headers);

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_add_member_to_lab_positive()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'amazon-lab';
        $this->parameters['role'] = 'User';
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/create?language='.$this->parameters['language'], $this->parameters, $this->headers);

        $response->assertStatus(200);
        $data = $response->json();
        if ($data['data'] !== null) {
            $this->assertArrayHasKey('invalid_emails', $data['data']);
            $this->assertArrayHasKey('invited_emails', $data['data']);
            $this->assertArrayHasKey('already_members', $data['data']);
        }
        $response->assertOk();
    }

    public function test_add_member_to_lab_negative()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'amazon-lab';
        $response = $this->post('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['wrong_slug'].'/create?language='.$this->parameters['language'], [], $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**get all the roles member management positive */
    public function test_get_roles_positive()
    {
        $response = $this->get('/api/v1/manage/member-management/get-roles?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**get all the roles member management positive */
    public function test_get_roles_negative()
    {
        $response = $this->post('/api/v1/manage/member-management/get-roles?language='.$this->parameters['wrong_language'], $this->parameters, $this->headers);
        $this->assertEquals(405, $response->getStatusCode());
    }

    /**Listing member management positive */
    public function test_organization_listing_positive()
    {
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']) {
            if ($data['data']['users'] !== null) {
                $this->assertArrayHasKey('id', $data['data']['users'][0]);
                $this->assertArrayHasKey('type', $data['data']['users'][0]);
                $this->assertArrayHasKey('invite_type', $data['data']['users'][0]);
                $this->assertArrayHasKey('name', $data['data']['users'][0]);
                $this->assertArrayHasKey('email', $data['data']['users'][0]);
                $this->assertArrayHasKey('username', $data['data']['users'][0]);
                $this->assertArrayHasKey('role', $data['data']['users'][0]);
                $this->assertArrayHasKey('invite_status', $data['data']['users'][0]);
                $this->assertArrayHasKey('auto_invite', $data['data']['users'][0]);
                $this->assertArrayHasKey('email_status', $data['data']['users'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Listing member management negative */
    public function test_organization_listing_negative()
    {
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['wrong_component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $response->assertStatus(404);
    }

    public function test_lab_listing_positive()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'amazon-lab';
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']) {
            if ($data['data']['users'] !== null) {
                $this->assertArrayHasKey('id', $data['data']['users'][0]);
                $this->assertArrayHasKey('type', $data['data']['users'][0]);
                $this->assertArrayHasKey('invite_type', $data['data']['users'][0]);
                $this->assertArrayHasKey('name', $data['data']['users'][0]);
                $this->assertArrayHasKey('email', $data['data']['users'][0]);
                $this->assertArrayHasKey('username', $data['data']['users'][0]);
                $this->assertArrayHasKey('role', $data['data']['users'][0]);
                $this->assertArrayHasKey('invite_status', $data['data']['users'][0]);
                $this->assertArrayHasKey('auto_invite', $data['data']['users'][0]);
                $this->assertArrayHasKey('email_status', $data['data']['users'][0]);
            }
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    public function test_lab_listing_negative()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'amazon-lab';
        $response = $this->get('/api/v1/manage/member-management/'.$this->parameters['wrong_component'].'/'.$this->parameters['slug'].'?language='.$this->parameters['language'], $this->headers);
        $response->assertStatus(404);
    }

    public function test_accept_members_join_request_positive()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'un-sdg-lab-1';
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/request/accept',
            [
                'language' => $this->parameters['language'],
            ],
            $this->headers
        );
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_accept_members_join_request_negative()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'un-sdg-lab-1';
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/request/accept',
            [
                'language' => $this->parameters['language'],
            ],
            $this->headers
        );
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_decline_members_join_request_positive()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'un-sdg-lab-2';
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/request/decline',
            [
                'language' => $this->parameters['language'],
            ],
            $this->headers
        );
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_decline_members_join_request_negative()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'un-sdg-lab-2';
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/request/decline',
            [
                'language' => $this->parameters['language'],
            ],
            $this->headers
        );
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**create member management positive */
    public function test_change_role_positive()
    {
        $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($this->parameters['component'], $this->parameters['slug']);
        $getid = MemberManagement::select('uuid')->where([
            ['email', '=', $this->parameters['invite_email'][0]],
            ['module_id', '=', $checkComponentBasedOnSlug['id']],
        ])->first();
        $this->parameters['id'] = $getid->uuid;
        $response = $this->post('/api/v1/manage/member-management/organization/change-role', $this->parameters, $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**create member management positive */
    public function test_change_role_negative()
    {
        $this->parameters['id'] = '';
        $response = $this->post('/api/v1/manage/member-management/organization/change-role', $this->parameters, $this->headers);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**download positive */
    public function test_download_sample_positive()
    {
        $response = $this->get('/api/v1/manage/member-management/download-sample?language='.$this->parameters['language'], $this->headers);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**download negative */
    public function test_download_sample_negative()
    {
        $response = $this->get('/api/v1/manage/member-management/download-sample?language='.$this->parameters['wrong_language'], $this->headers);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Delete member management positive */
    public function test_delete_organization_member_positive()
    {
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language='.$this->parameters['language'],
            [
                'email'=> $this->parameters['invite_email'],
            ],
            $this->headers
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**Delete member management negative */
    public function test_delete_organization_member_negative()
    {
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language='.$this->parameters['language'],
            [
                'email'=> '',
            ],
            $this->headers
        );
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function test_delete_lab_member_positive()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'amazon-lab';
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language='.$this->parameters['language'],
            [
                'email'=> $this->parameters['invite_email'],
            ],
            $this->headers
        );
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_lab_member_negative()
    {
        $this->parameters['component'] = 'lab';
        $this->parameters['slug'] = 'amazon-lab';
        $response = $this->post(
            '/api/v1/manage/member-management/'.$this->parameters['component'].'/'.$this->parameters['slug'].'/delete?language='.$this->parameters['language'],
            [
                'email'=> '',
            ],
            $this->headers
        );
        $this->assertEquals(422, $response->getStatusCode());
    }
}
