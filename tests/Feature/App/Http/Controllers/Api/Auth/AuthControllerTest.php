<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;
use Tests\TestCase;

/** 
 * Class AuthControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Auth\AuthController
 */
final class AuthControllerTest extends TestCase
{

    /**login positive test cases */
    public function testLoginPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/login', ["email" => "rajan@prepr.org", "password" => "Prepr@123"]);
        $this->assertEquals(200, $response->getStatusCode());
        
    }
    /**Login Negative Wrong Password*/
    public function testLoginNegativeWithWrongPassword(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/login', ["email" => "rajan@prepr.org", "password" => "Prepr@1234"]);
        $this->assertEquals(401, $response->getStatusCode());
    }
    /**login negative With Wrong Email*/
    public function testLoginNegativeWithWrongEmail(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/login', ["email" => "rajan@prepr.orgs", "password" => "Prepr@1234"]);
        $this->assertEquals(403, $response->getStatusCode());
    }
    /**login negative */
    public function testLoginNegativeWithoutParameter(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/login');
        $this->assertEquals(403, $response->getStatusCode());
    }
    /**Positive user registeration test cases */
    public function testRegisterUserPositive(): void
    {
        /** @todo This test is complete. */
        // $response = $this->postJson('/api/v1/auth/register', ["language_id" => "1", "username" => "Rajandhand1", "email" => "rajan@prepr.org1", "first_name" => "rajan1", "last_name" => "last1", "password" => "Prepr@123", "password_confirmation" => "Prepr@123", "device_platform" => "web", "user_type" => "user", "status" => "looking_team", "country_code" => "+91", "phone_number" => "7589280802", "organization_name" => "organizatoin1", "vanity_link" => "preprlab"]);
        // $this->assertEquals(200, $response->getStatusCode());
    }
    /**Negative user registeration test cases */
    public function testRegisterUserNegativeWithoutParameters(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/register');
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**Negative user registeration with exists records test cases */
    public function testRegisterUserNegativeWithExistsRecords(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/register', ["language_id" => "1", "username" => "Rajandhand1", "email" => "rajan@prepr.org1", "first_name" => "rajan1", "last_name" => "last1", "password" => "Prepr@123", "password_confirmation" => "Prepr@123", "device_platform" => "web", "user_type" => "user", "status" => "looking_team", "country_code" => "+91", "phone_number" => "7589280802", "organization_name" => "organizatoin1", "vanity_link" => "preprlab"]);
        $this->assertEquals(403, $response->getStatusCode());
    }
    /**Positive test case for forget password */
    public function testForgetPasswordPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/forget-password', ["email" => "rajan@prepr.org"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
       /**Negative test case for forget password */
    public function testForgetPasswordNegativeWithoutParameters(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/forget-password');
        $this->assertEquals(403, $response->getStatusCode());
    }
         /**Positive test case for Username */
    public function testCheckUsernamePositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkusername', ["username" => "rajandhand2"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
    /**Negative test case of username */
    public function testCheckUsernameNegativeWithoutParameter(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkusername');
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**Positive test case check Email */
    public function testCheckEmailPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkemail', ["email" => "rajan@prepr.org1"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
      /**Negative test case check Email */
    public function testCheckEmailNegative(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkemail', ["email" => "rajan@prepr.org"]);
        $this->assertEquals(403, $response->getStatusCode());
    }

      /**Negative test case check Email */
      public function testCheckEmailNegativeWithoutparameters(): void
      {
          /** @todo This test is complete. */
          $response = $this->postJson('/api/v1/auth/checkemail');
          $this->assertEquals(403, $response->getStatusCode());
      }
     /**Positive test case for check phone number */
    public function testCheckPhonePositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkphone', ["phone_number" => "9646080803"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
      /**Negative test case for check phone number */
    public function testCheckPhoneNegativeWithoutParameters(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkphone');
        $this->assertEquals(403, $response->getStatusCode());
    }
     /**Negative test case for check phone number */
     public function testCheckPhoneNegativeWithExistsPhoneNumber(): void
     {
         /** @todo This test is complete. */
         $response = $this->postJson('/api/v1/auth/checkphone', ["phone_number" => "9646080802"]);
         $this->assertEquals(403, $response->getStatusCode());
     }
      /**Positive test cases for organizations */
    public function testCheckOrganizationPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkorgnization', ["name" => "Preprs"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
     /**Negative test cases for organizations */
    public function testCheckOrgnizationNegativeWithParameters(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/checkorgnization', ["name" => "Prepr"]);
        $this->assertEquals(403, $response->getStatusCode());
    }
       /**Negative test cases for organizations */
      public function testCheckOrgnizationNegativeWithoutParameters(): void
      {
          /** @todo This test is complete. */
          $response = $this->postJson('/api/v1/auth/checkorgnization');
          $this->assertEquals(403, $response->getStatusCode());
      }
      /**Positive test case for Send Otp */
    public function testSendOtpPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/send-otp', ["email" => "rajan@prepr.org"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
     
    /**Negative Test cases for send otp */
    public function testSendOtpNegativeWithoutParameters(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/send-otp');
        $this->assertEquals(403, $response->getStatusCode());
    }
    
    /**Negative Test cases for send otp */
    public function testSendOtpNegativeWithParameters(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/send-otp',["email" => "rajan@prepr.orgs"]);
        $this->assertEquals(403, $response->getStatusCode());
    }
    /**Positive Test cases for verify otp */
    public function testVerifyOtpPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/send-otp', ["email" => "rajan@prepr.org", "otp" => "1234"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
     /**Negative test cases for verify otp */
    public function testVerifyOtpNegative(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/send-otp');
        $this->assertEquals(403, $response->getStatusCode());
    }
     /**Positive test cases for referal-code */
    public function testReferalCodePositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/verify-invite-code', ["mycode" => "rajandhand"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
     /**Negative test cases for Referal code */
    public function testReferalCodeNegative(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/verify-invite-code');
        $this->assertEquals(403, $response->getStatusCode());
    }
      /**Positive test cases for positive */
    public function testResetPasswordPositive(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/reset-password', ["email" => "rajan@prepr.org", "password" => "Prepr@123", "password_confirmation" => "Prepr@123"]);
        $this->assertEquals(200, $response->getStatusCode());
    }
     /**Negative test cases for resetpassword */
    public function testResetPasswordNegative(): void
    {
        /** @todo This test is complete. */
        $response = $this->postJson('/api/v1/auth/reset-password');
        $this->assertEquals(403, $response->getStatusCode());
    }
}
