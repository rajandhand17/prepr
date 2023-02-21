<?php

namespace Tests\Feature\app\Http\Controllers\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /**Login Positive */
    public function test_post_login_positive()
    {
        $email = "rajan@prepr.org";
        $password = "Prepr@123";
        $response = $this->post('/api/v1/auth/login', ["email" => $email, "password" => $password, "language" => "en"]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }
    /**Wrong email address */
    public function test_post_login_wrong_email_negative()
    {
        $email = "rajan@prepr.orgs";
        $password = "Prepr@123";
        $response = $this->post('/api/v1/auth/login', ["email" => $email, "password" => $password, "language" => "en"]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Wrong email address */
    public function test_post_login_wrong_password_negative()
    {
        $email = "rajan@prepr.orgs";
        $password = "Prepr@1234";
        $response = $this->post('/api/v1/auth/login', ["email" => $email, "password" => $password, "language" => "en"]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Successfull Positive */
    public function test_register_positive()
    {
        $language = "en";
        $username = "Rajandhanda";
        $email = "rajan@prepr.orga";
        $first_name = "rajan";
        $last_name = "dhand";
        $password = "Prepr@123";
        $password_confirmation = "Prepr@123";
        $device_platform = "web";
        $user_type = "teacher";
        $purpose = "looking_team";
        $country_code = "+91";
        $phone_number = "964608080288";
        $two_factor_verification = "1";
        $organization_name = "organization";
        $vanity_link = "prepr";
        $response = $this->post("/api/v1/auth/register", ["language" => $language, "username" => $username, "email" => $email, "first_name" => $first_name, "last_name" => $last_name, "password" => $password, "password_confirmation" => $password_confirmation, "device_platform" => $device_platform, "user_type" => $user_type, "purpose" => $purpose, "country_code" => $country_code, "phone_number" => $phone_number, "two_factor_verification" => $two_factor_verification, "organization_name" => $organization_name, "vanity_link" => $vanity_link]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }

    /**Negative Register */
    public function test_register_negative()
    {
        $response = $this->post("/api/v1/auth/register");
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Negative Registered */
    public function test_register_negative_exists()
    {
        $language = "en";
        $username = "Rajandhanda";
        $email = "rajan@prepr.orga";
        $first_name = "rajan";
        $last_name = "dhand";
        $password = "Prepr@123";
        $password_confirmation = "Prepr@123";
        $device_platform = "web";
        $user_type = "teacher";
        $purpose = "looking_team";
        $country_code = "+91";
        $phone_number = "964608080288";
        $two_factor_verification = "1";
        $organization_name = "organization";
        $vanity_link = "prepr";
        $response = $this->post("/api/v1/auth/register", ["language" => $language, "username" => $username, "email" => $email, "first_name" => $first_name, "last_name" => $last_name, "password" => $password, "password_confirmation" => $password_confirmation, "device_platform" => $device_platform, "user_type" => $user_type, "purpose" => $purpose, "country_code" => $country_code, "phone_number" => $phone_number, "two_factor_verification" => $two_factor_verification, "organization_name" => $organization_name, "vanity_link" => $vanity_link]);
        $this->assertEquals(422, $response->getStatusCode());
    }
    /**Check user name positive */
    public function test_post_check_username_positive()
    {
        $username = "rajandhand20";
        $language = "en";
        $response = $this->post("/api/v1/auth/checkusername", ["username" => $username, "language" => $language]);
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
        $username = "rajandhand";
        $language = "en";
        $response = $this->post("/api/v1/auth/checkusername", ["username" => $username, "language" => $language]);
        $this->assertEquals(422, $response->getStatusCode());
    }
    /**Check user name negative */
    public function test_post_check_username_negative()
    {
        $response = $this->post("/api/v1/auth/checkusername");
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_email_positive()
    {
        $email = "rajandhand17@gmail.com";
        $language = "en";
        $response = $this->post("/api/v1/auth/checkemail", ["email" => $email, "language" => $language]);
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
        $email = "rajan@prepr.org";
        $language = "en";
        $response = $this->post("/api/v1/auth/checkemail", ["email" => $email, "language" => $language]);
        $this->assertEquals(422, $response->getStatusCode());
    }
    /**Check user name negative */
    public function test_post_check_email_negative()
    {
        $response = $this->post("/api/v1/auth/checkemail");
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_phone_positive()
    {
        $phone_number = "9646080805";
        $language = "en";
        $response = $this->post("/api/v1/auth/checkphone", ["phone_number" => $phone_number, "language" => $language]);
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
        $phone_number = "9646080802";
        $language = "en";
        $response = $this->post("/api/v1/auth/checkphone", ["phone_number" => $phone_number, "language" => $language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_phone_negative()
    {
        $response = $this->post("/api/v1/auth/checkphone");
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**send otp */
    public function test_post_send_otp_positive()
    {
        $response = $this->post("/api/v1/auth/send-otp", ["email" => "rajan@prepr.org", "purpose" => "two_factor_verification", "language" => "en"]);
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
        $email = "rajan@prepr.orgs";
        $language = "en";
        $response = $this->post("/api/v1/auth/send-otp", ["email" => $email, "language" => $language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**verify otp */
    public function test_post_verify_otp_positive()
    {
        $email = "rajan@prepr.orgs";
        $language = "en";
        $otp = "4565";
        $response = $this->post("/api/v1/auth/verify-otp", ["email" => $email, "otp" => $otp, "language" => $language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }
    /** Forget Password */
    public function test_post_forget_password_positive()
    {
        $email = "rajan@prepr.org";
        $language = "en";
        $response = $this->post("/api/v1/auth/forget-password", ["email" => $email, "language" => $language]);
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
        $email = "rajan@prepr.orgss";
        $language = "en";
        $response = $this->post("/api/v1/auth/forget-password", ["email" => $email, "language" => $language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** Reset Password */
    public function test_post_reset_password_positive()
    {
        $email = "rajan@prepr.org";
        $password = "Prepr@123";
        $password_confirmation = "Prepr@123";
        $otp = "4565";
        $language = "en";
        $response = $this->post("/api/v1/auth/reset-password", ["email" => $email, "password" => $password, "password_confirmation" => $password_confirmation, "otp" => $otp, "language" => $language]);
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
        $email = "rajan@prepr.orgsd";
        $password = "Prepr@123";
        $password_confirmation = "Prepr@123";
        $otp = "4565";
        $language = "en";
        $response = $this->post("/api/v1/auth/reset-password", ["email" => $email, "password" => $password, "password_confirmation" => $password_confirmation, "otp" => $otp, "language" => $language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** Reset Password */
    public function test_post_verify_invite_code_positive()
    {   
        $referal_code="rajandhand2023";
        $language="en";
        $response = $this->post("/api/v1/auth/verify-invite-code", ["referal_code" =>$referal_code, "language" =>$language]);
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
        $referal_code="rajandhand2";
        $language="en";
        $response = $this->post("/api/v1/auth/verify-invite-code", ["referal_code" =>$referal_code, "language" =>$language]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /** Reset Password */
    public function test_post_verify_two_factor_positive()
    {   
        $email="rajan@prepr.org";
        $otp="6814";
        $language="en";
        $response = $this->post("/api/v1/auth/verify-two-factor", ["email" =>$email, "otp" =>$otp,"language" =>$language]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success'] === true) {
            $response->assertOk();
        } else {
            $this->fail();
        }
    }
    /** Reset Password negative*/
    public function test_post_verify_two_factor_negative()
    {   
        $language="en";
        $response = $this->post("/api/v1/auth/verify-invite-code", ["language" =>$language]);
        $this->assertEquals(422, $response->getStatusCode());
    }
}
