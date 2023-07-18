<?php

namespace Tests\Feature\app\Http\Controllers\Api\Auth;

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
        $this->username = 'Rajandhands';
        $this->wrong_username = 'Rajandhandsas';
        $this->email = 'rajan@prepr.orgs';
        $this->wrong_email = 'rajanwrong@prepr.orgs';
        $this->first_name = 'rajan';
        $this->last_name = 'dhand';
        $this->password = 'Prepr@123';
        $this->password_confirmation = 'Prepr@123';
        $this->wrong_password = 'Prepr';
        $this->device_platform = 'web';
        $this->user_type = 'student';
        $this->purpose = 'looking_team';
        $this->country_code = '+91';
        $this->phone_number = '9646080802';
        $this->wrong_phone_number = '9646080805';
        $this->purpose_send_otp = 'two_factor_verification';
        $this->referral_code = 'Rajandhands2023';
        $this->wrong_referral_code = 'rajandhandd';
        $this->register_type = 'organization';
        $this->organization_name = 'prepr sds';
    }

    /**Successfull Positive */
    public function test_register_positive()
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
            'register_type'         => $this->register_type,
            'organization_name'     => $this->organization_name,
        ]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            if ($response->assertOk()) {
                $records = User::select('otp')->where('email', $this->email)->first();
                $verifyuser = $this->post('/api/v1/auth/verify-otp', ['language' => $this->language, 'email' => $this->email, 'otp'=>$records->otp]);
                $verifyuser->assertStatus(200);
                $datavarify = $verifyuser->json();
                if ($datavarify['success'] == true) {
                    $verifyuser->assertOk();
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
            'language' => $this->language, 
            'username' => $this->username, 
            'email' => $this->email, 
            'first_name' => $this->first_name, 
            'last_name' => $this->last_name, 
            'password' => $this->password, 
            'password_confirmation' => $this->password_confirmation, 
            'device_platform' => $this->device_platform, 
            'user_type' => $this->user_type, 
            'purpose' => $this->purpose, 
            'country_code' => $this->country_code, 
            'phone_number' => $this->phone_number
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
            if (isset($data['data']['token'])) {
                $response->assertOk();
            } else {
                $userrecords = User::select('otp')->where('email', $this->email)->first();
                $twofactorresponse = $this->post('/api/v1/auth/verify-two-factor', ['email' => $this->email, 'otp' => $userrecords->otp, 'language' => $this->language]);

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
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name positive */
    public function test_post_check_username_positive()
    {
        $response = $this->post('/api/v1/auth/checkusername', ['username' => $this->wrong_username, 'language' => $this->language]);
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
        $response = $this->post('/api/v1/auth/checkusername', ['username' => $this->username, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_username_negative()
    {
        $response = $this->post('/api/v1/auth/checkusername');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_email_positive()
    {
        $response = $this->post('/api/v1/auth/checkemail', ['email' => $this->wrong_email, 'language' => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Check user name negative */
    public function test_post_check_email_notexists_negative()
    {
        $response = $this->post('/api/v1/auth/checkemail', ['email' => $this->email, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_email_negative()
    {
        $response = $this->post('/api/v1/auth/checkemail');
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_phone_positive()
    {
        $response = $this->post('/api/v1/auth/checkphone', ['phone_number' => $this->wrong_phone_number, 'language' => $this->language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Check user name negative */
    public function test_post_check_phone_notexists_negative()
    {
        $response = $this->post('/api/v1/auth/checkphone', ['phone_number' => $this->phone_number, 'language' => $this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_phone_negative()
    {
        $response = $this->post('/api/v1/auth/checkphone');
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
        $response = $this->post('/api/v1/auth/verify-invite-code', ['referral_code' =>$this->referral_code, 'language' =>$this->language]);
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
        $response = $this->post('/api/v1/auth/verify-invite-code', ['referral_code' =>$this->wrong_referral_code, 'language' =>$this->language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** Two Factor negative*/
    public function test_post_verify_two_factor_negative()
    {
        $language = 'en';
        $response = $this->post('/api/v1/auth/verify-invite-code', ['language' =>$language]);
        $this->assertEquals(422, $response->getStatusCode());
    }
}
