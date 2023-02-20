<?php
namespace Tests\Feature\app\Http\Controllers\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
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
        $response = $this->post('/api/v1/auth/login',["email"=>"rajan@prepr.org","password"=>"Prepr@123"]);
        $response->assertStatus(200);
        $data = $response->json();
        if ($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }
    /**Wrong email address */
    public function test_post_login_wrong_email_negative(){
        $response = $this->post('/api/v1/auth/login',["email"=>"rajan@prepr.orgs","password"=>"Prepr@123"]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**Wrong email address */
    public function test_post_login_wrong_password_negative(){
        $response = $this->post('/api/v1/auth/login',["email"=>"rajan@prepr.org","password"=>"Prepr@1234"]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**Successfull Positive */
    public function test_register_positive()
    {
        $response=$this->post("/api/v1/auth/register",["preferred_language"=>"en","username"=>"Rajandhan","email"=>"rajan@prepr.or","first_name"=>"rajas","last_name"=>"dhanl","password"=>"Prepr@13","password_confirmation"=>"Prepr@13","device_platform"=>"web","user_type"=>"teacherd","purpose"=>"looking_teams","country_code"=>"+91","phone_number"=>"9646080505","two_factor_verification"=>"1","organization_name"=>"organizatoins","vanity_link"=>"prepra","referal_code"=>"rajan2a"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }

    /**Negative Register */
    public function test_register_negative()
    {
        $response=$this->post("/api/v1/auth/register");
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**Negative Registered */
    public function test_register_negative_exists()
    {
        $response=$this->post("/api/v1/auth/register",["preferred_language"=>"en","username"=>"Rajandhands","email"=>"rajan@prepr.orgs","first_name"=>"rajans","last_name"=>"dhand","password"=>"Prepr@123","password_confirmation"=>"Prepr@123","device_platform"=>"web","user_type"=>"teacher","purpose"=>"looking_team","country_code"=>"+91","phone_number"=>"9646080802","two_factor_verification"=>1,"organization_name"=>"organizatoin","vanity_link"=>"prepr","referal_code"=>"rajan20"]);
        $this->assertEquals(403, $response->getStatusCode());
    }
  /**Check user name positive */
    public function test_post_check_username_positive()
    {
        $response=$this->post("/api/v1/auth/checkusername",["username"=>"rajandhand1"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }
  /**Check user name negative */
    public function test_post_check_username_notexists_negative()
    {
        $response=$this->post("/api/v1/auth/checkusername",["username"=>"rajandhand"]);
        $this->assertEquals(403, $response->getStatusCode());
    }
/**Check user name negative */
    public function test_post_check_username_negative()
    {
        $response=$this->post("/api/v1/auth/checkusername");
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_email_positive()
    {
        $response=$this->post("/api/v1/auth/checkemail",["email"=>"rajan14@prepr.org"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }
  /**Check user name negative */
    public function test_post_check_email_notexists_negative()
    {
        $response=$this->post("/api/v1/auth/checkemail",["email"=>"rajan@prepr.org"]);
        $this->assertEquals(403, $response->getStatusCode());
    }
/**Check user name negative */
    public function test_post_check_email_negative()
    {
        $response=$this->post("/api/v1/auth/checkemail");
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**Check email positive */
    public function test_post_check_phone_positive()
    {
        $response=$this->post("/api/v1/auth/checkphone",["phone_number"=>"9646080805"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }

    /**Check user name negative */
    public function test_post_check_phone_notexists_negative()
    {
        $response=$this->post("/api/v1/auth/checkphone",["phone_number"=>"9646080802"]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**Check user name negative */
    public function test_post_check_phone_negative()
    {
        $response=$this->post("/api/v1/auth/checkphone");
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**send otp */
    public function test_post_send_otp_positive()
    {
        $response=$this->post("/api/v1/auth/send-otp",["email"=>"rajan@prepr.org"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }

    /**send otp */
    public function test_post_send_otp_negative()
    {
        $response=$this->post("/api/v1/auth/send-otp",["email"=>"rajan@prepr.orgs"]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**verify otp */
    public function test_post_verify_otp_positive()
    {
        $response=$this->post("/api/v1/auth/verify-otp",["email"=>"rajan@prepr.org","otp"=>"4565"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }
      /** Forget Password */
    public function test_post_forget_password_positive()
    {
        $response=$this->post("/api/v1/auth/forget-password",["email"=>"rajan@prepr.org"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }

    /**Forget password negative */
    public function test_post_forget_password_negative()
    {
        $response=$this->post("/api/v1/auth/forget-password",["email"=>"rajan@prepr.orgss"]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** Reset Password */
    public function test_post_reset_password_positive()
    {
        $response=$this->post("/api/v1/auth/reset-password",["email"=>"rajan@prepr.org","password"=>"Prepr@123","password_confirmation"=>"Prepr@123","otp"=>"4565"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }

    /** Reset Password */
    public function test_post_reset_password_negative()
    {
        $response=$this->post("/api/v1/auth/reset-password",["email"=>"rajan@prepr.org","password"=>"Prepr@123","password_confirmation"=>"Prepr@123","otp"=>"4565"]);
        $this->assertEquals(403, $response->getStatusCode());

    }

    /** Reset Password */
    public function test_post_verify_invite_code_positive()
    {
        $response=$this->post("/api/v1/auth/verify-invite-code",["referal_code"=>"rajan20"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }

    /** Reset Password negative*/
    public function test_post_verify_invite_code_negative()
    {
        $response=$this->post("/api/v1/auth/verify-invite-code",["referal_code"=>"rajan2045"]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** Reset Password */
    public function test_post_verify_two_factor_positive()
    {
        $response=$this->post("/api/v1/auth/verify-two-factor",["email"=>"rajan@prepr.org","otp"=>"6814"]);
        $response->assertStatus(200);
        $data = $response->json();
        if($data['success']===true){
             $response->assertOk();
        }else{
            $this->fail();
        }
    }
    /** Reset Password negative*/
    public function test_post_verify_two_factor_negative()
    {
        $response=$this->post("/api/v1/auth/verify-invite-code");
        $this->assertEquals(403, $response->getStatusCode());
    }

}
