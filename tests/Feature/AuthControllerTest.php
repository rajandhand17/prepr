<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->language = 'en';
        $this->username = 'Rajan7016';
        $this->username_with_organization = 'Dhand7016';
        $this->wrong_username = 'rajan1994';
        $this->email = 'rajandhand17@gmail.com';
        $this->email_with_organization = 'rajan@amazon.com';
        $this->wrong_email = 'rajanwrong@amazon.com';
        $this->first_name = 'rajan';
        $this->last_name = 'dhand';
        $this->password = 'Prepr@123';
        $this->password_confirmation = 'Prepr@123';
        $this->wrong_password = 'Prepr@1234';
        $this->device_platform = 'web';
        $this->user_type = 'student';
        $this->purpose = 'looking_team';
        $this->country_code = '+91';
        $this->phone_number = '9878245683';
        $this->phone_number_with_organization = '8872845176';
        $this->wrong_phone_number = '7589780802';
        $this->purpose_send_otp = 'two_factor_verification';
        $this->referral_code = 'Rajan70162023';
        $this->wrong_referral_code = 'rajan1994';
        $this->register_type_organization = 'organization';
        $this->register_type_user = 'user';
        $this->organization_name = 'RForm';
    }

    /**Successfull Positive */
    public function test_register_positive_with_user()
    {
        $response = $this->post('/api/v1/auth/register', [
            'language'               => $this->language,
            'username'               => $this->username,
            'email'                  => $this->email,
            'first_name'             => $this->first_name,
            'last_name'              => $this->last_name,
            'password'               => $this->password,
            'password_confirmation'  => $this->password_confirmation,
            'device_platform'        => $this->device_platform,
            'user_type'              => $this->user_type,
            'purpose'                => $this->purpose,
            'country_code'           => $this->country_code,
            'phone_number'           => $this->phone_number,
            'register_type'          => $this->register_type_user,
        ]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            if ($response->assertOk()) {
                $records = User::select('otp')->where('email', $this->email)->first();
                $verifyuser = $this->post('/api/v1/auth/verify-account', ['language' => $this->language, 'email' => $this->email, 'otp'=>$records->otp]);
                $verifyuser->assertStatus(200);
                $datavarify = $verifyuser->json();
                if ($datavarify['success'] == true) {
                    if ($datavarify['data'] !== null) {
                        $this->assertArrayHasKey('id', $datavarify['data']);
                        $this->assertArrayHasKey('preferred_language', $datavarify['data']);
                        $this->assertArrayHasKey('first_name', $datavarify['data']);
                        $this->assertArrayHasKey('last_name', $datavarify['data']);
                        $this->assertArrayHasKey('full_name', $datavarify['data']);
                        $this->assertArrayHasKey('username', $datavarify['data']);
                        $this->assertArrayHasKey('email', $datavarify['data']);
                        $this->assertArrayHasKey('profile_image', $datavarify['data']);
                        $this->assertArrayHasKey('two_factor_verification', $datavarify['data']);
                        $this->assertArrayHasKey('user_points', $datavarify['data']);
                        $this->assertArrayHasKey('user_rank', $datavarify['data']);
                        $this->assertArrayHasKey('verified_user', $datavarify['data']);
                        $this->assertArrayHasKey('referral_code', $datavarify['data']);
                        $this->assertArrayHasKey('is_profile_completed', $datavarify['data']);
                    }
                    $verifyuser->assertOk();
                }
            }
        } else {
            $this->fail();
        }
    }

    /**Successfull Positive with organization*/
    public function test_register_positive_with_organization()
    {
        $response = $this->post('/api/v1/auth/register', [
            'language'               => $this->language,
            'username'               => $this->username_with_organization,
            'email'                  => $this->email_with_organization,
            'first_name'             => $this->first_name,
            'last_name'              => $this->last_name,
            'password'               => $this->password,
            'password_confirmation'  => $this->password_confirmation,
            'device_platform'        => $this->device_platform,
            'user_type'              => $this->user_type,
            'purpose'                => $this->purpose,
            'country_code'           => $this->country_code,
            'phone_number'           => $this->phone_number_with_organization,
            'register_type'          => $this->register_type_organization,
            'organization_title'     => $this->organization_name,
        ]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            if ($response->assertOk()) {
                $records = User::select('otp')->where('email', $this->email_with_organization)->first();
                $verifyuser = $this->post('/api/v1/auth/verify-account', ['language' => $this->language, 'email' => $this->email_with_organization, 'otp'=>$records->otp]);

                $verifyuser->assertStatus(200);

                $datavarify = $verifyuser->json();
                if ($datavarify['success'] == true) {
                    if ($datavarify['data'] !== null) {
                        $this->assertArrayHasKey('id', $datavarify['data']);
                        $this->assertArrayHasKey('preferred_language', $datavarify['data']);
                        $this->assertArrayHasKey('first_name', $datavarify['data']);
                        $this->assertArrayHasKey('last_name', $datavarify['data']);
                        $this->assertArrayHasKey('full_name', $datavarify['data']);
                        $this->assertArrayHasKey('username', $datavarify['data']);
                        $this->assertArrayHasKey('email', $datavarify['data']);
                        $this->assertArrayHasKey('profile_image', $datavarify['data']);
                        $this->assertArrayHasKey('two_factor_verification', $datavarify['data']);
                        $this->assertArrayHasKey('user_points', $datavarify['data']);
                        $this->assertArrayHasKey('user_rank', $datavarify['data']);
                        $this->assertArrayHasKey('verified_user', $datavarify['data']);
                        $this->assertArrayHasKey('referral_code', $datavarify['data']);
                        $this->assertArrayHasKey('is_profile_completed', $datavarify['data']);
                    }
                    $verifyuser->assertOk();
                } else {
                    $this->fail();
                }
            }
        } else {
            $this->fail();
        }
    }

    /**Negative Register */
    public function test_register_negative()
    {
        $response = $this->post('/api/v1/auth/register');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Negative Registered */
    public function test_register_negative_exists()
    {
        $response = $this->post('/api/v1/auth/register', [
            'language'              => $this->language,
            'username'              => $this->username,
            'email'                 => $this->email,
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'password'              => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'device_platform'       => $this->device_platform,
            'user_type'             => $this->user_type,
            'purpose'               => $this->purpose,
            'country_code'          => $this->country_code,
            'phone_number'          => $this->phone_number,
        ]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**login Positive */
    public function test_post_login_positive()
    {
        $response = $this->post('/api/v1/auth/login', ['email' => $this->email, 'password' => $this->password, 'language' => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            if ($data['data'] !== null) {
                $this->assertArrayHasKey('token', $data['data']['token']);
                $this->assertArrayHasKey('id', $data['data']['user']);
                $this->assertArrayHasKey('preferred_language', $data['data']['user']);
                $this->assertArrayHasKey('first_name', $data['data']['user']);
                $this->assertArrayHasKey('last_name', $data['data']['user']);
                $this->assertArrayHasKey('full_name', $data['data']['user']);
                $this->assertArrayHasKey('username', $data['data']['user']);
                $this->assertArrayHasKey('email', $data['data']['user']);
                $this->assertArrayHasKey('profile_image', $data['data']['user']);
                $this->assertArrayHasKey('two_factor_verification', $data['data']['user']);
                $this->assertArrayHasKey('user_points', $data['data']['user']);
                $this->assertArrayHasKey('user_rank', $data['data']['user']);
                $this->assertArrayHasKey('verified_user', $data['data']['user']);
                $this->assertArrayHasKey('referral_code', $data['data']['user']);
                $this->assertArrayHasKey('is_profile_completed', $data['data']['user']);
            }
            if (isset($data['data']['token'])) {
                $response->assertOk();
            } else {
                $userrecords = User::select('otp')->where('email', $this->email)->first();
                $twofactorresponse = $this->post('/api/v1/auth/two-factor-verification', ['email' => $this->email, 'otp' => $userrecords->otp, 'language' => $this->language]);
                $twofactorresponse->assertStatus(200);
            }
        } else {
            $this->fail();
        }
    }

    /**Wrong email address */
    public function test_post_login_wrong_email_negative()
    {
        $response = $this->post('/api/v1/auth/login', ['email' => $this->wrong_email, 'password' => $this->password, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Wrong email address */
    public function test_post_login_wrong_password_negative()
    {
        $response = $this->post('/api/v1/auth/login', ['email' => $this->email, 'password' => $this->wrong_password, 'language' => $this->language]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**Check user name positive */
    public function test_post_check_username_positive()
    {
        $response = $this->post('/api/v1/auth/check-username', ['username' => $this->wrong_username, 'language' => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Check user name negative */
    public function test_post_check_username_notexists_negative()
    {
        $response = $this->post('/api/v1/auth/check-username', ['username' => $this->username, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_username_negative()
    {
        $response = $this->post('/api/v1/auth/check-username');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_email_positive()
    {
        $response = $this->post('/api/v1/auth/check-email', ['email' => $this->wrong_email, 'language' => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Check user name negative */
    public function test_post_check_email_not_exists_negative()
    {
        $response = $this->post('/api/v1/auth/check-email', ['email' => $this->email, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_email_negative()
    {
        $response = $this->post('/api/v1/auth/check-email');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_phone_positive()
    {
        $response = $this->post('/api/v1/auth/check-phone', ['phone_number' => $this->wrong_phone_number, 'language' => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Check user name negative */
    public function test_post_check_phone_not_exists_negative()
    {
        $response = $this->post('/api/v1/auth/check-phone', ['phone_number' => $this->phone_number, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_phone_negative()
    {
        $response = $this->post('/api/v1/auth/check-phone');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**send otp */
    public function test_post_send_otp_positive()
    {
        $response = $this->post('/api/v1/auth/send-otp', ['email' =>$this->email, 'purpose' =>$this->purpose_send_otp, 'language' =>$this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**send otp */
    public function test_post_send_otp_negative()
    {
        $response = $this->post('/api/v1/auth/send-otp', ['email' => $this->email, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** Forget Password */
    public function test_post_forget_password_positive()
    {
        $response = $this->post('/api/v1/auth/forget-password', ['email' => $this->email, 'language' =>$this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Forget password negative */
    public function test_post_forget_password_negative()
    {
        $response = $this->post('/api/v1/auth/forget-password', ['language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** Reset Password */
    public function test_post_reset_password_positive()
    {
        $records = User::select('otp')->where('email', $this->email)->first();
        $response = $this->post('/api/v1/auth/reset-password', ['email' => $this->email, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'otp' => $records->otp, 'language' => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** Reset Password */
    public function test_post_reset_password_negative()
    {
        $response = $this->post('/api/v1/auth/reset-password', ['email' => $this->wrong_email, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** invit code */
    public function test_post_verify_invite_code_positive()
    {
        $response = $this->post('/api/v1/auth/verify-referral-code', ['referral_code' =>$this->referral_code, 'language' =>$this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /** Reset Password negative*/
    public function test_post_verify_invite_code_negative()
    {
        $response = $this->post('/api/v1/auth/verify-referral-code', ['referral_code' =>$this->wrong_referral_code, 'language' =>$this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }
}
