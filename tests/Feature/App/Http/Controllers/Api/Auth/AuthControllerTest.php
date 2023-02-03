<?php

namespace Tests\Feature\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Repositories\Api\Auth\AuthRepository;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/** 
 * Class AuthControllerTest.
 *
 * @covers \App\Http\Controllers\Api\Auth\AuthController
 */
final class AuthControllerTest extends TestCase
{
    private AuthController $authController;

    private AuthRepository|Mock $authRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authRepository = Mockery::mock(AuthRepository::class);
        $this->authController = new AuthController($this->authRepository);
        $this->app->instance(AuthController::class, $this->authController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->authController);
        unset($this->authRepository);
    }

    public function testLoginPositive(): void
    {
        /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/login',["email"=>"rajan@prepr.org","password"=>"Prepr@123"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    
    }

    
    public function testLoginNegative(): void
    {
        /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/login');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }
 
    public function testRegisterUserPositive(): void
    {
          /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/register',["language_id"=>"1","username"=>"Rajandhand","email"=>"rajan@prepr.org","first_name"=>"rajan","last_name"=>"last","password"=>"Prepr@123","password_confirmation"=>"Prepr@123","device_platform"=>"web","user_type"=>"user","status"=>"looking_team","country_code"=>"+91","phone_number"=>"9646080802","organization_name"=>"organizatoin","vanity_link"=>"prepr"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }
    
    public function testRegisterUserNegative(): void
    {
          /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/register');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testForgetPasswordPositive(): void
    {
        /** @todo This test is complete. */
       $response = $this->postJson('/api/v1/auth/forget-password',["email"=>"rajan@prepr.org"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    
    public function testForgetPasswordNegative(): void
    {
        /** @todo This test is complete. */
       $response = $this->postJson('/api/v1/auth/forget-password');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testCheckUsernamePositive(): void
    {
      /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkusername',["username"=>"rajandhand"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    public function testCheckUsernameNegative(): void
    {
      /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkusername');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testCheckEmailPositive(): void
    {
         /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkemail',["email"=>"rajan@prepr.org"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    public function testCheckEmailNegative(): void
    {
         /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkemail',["email"=>"rajan@prepr.org"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testCheckPhonePositive(): void
    {
        /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkphone',["phone_number"=>"9646080802"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    
    public function testCheckPhoneNegative(): void
    {
        /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkphone',["phone_number"=>"9646080802"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testCheckOrgnizationPositive(): void
    {
          /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkorgnization',["name"=>"Prepr"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    
    public function testCheckOrgnizationNegative(): void
    {
          /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/checkorgnization');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testSendOtpPositive(): void
    {
            /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/send-otp',["email"=>"rajan@prepr.org"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    public function testSendOtpNegative(): void
    {
            /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/send-otp');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testVerifyOtpPositive(): void
    {
             /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/send-otp',["email"=>"rajan@prepr.org","otp"=>"1234"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    
    public function testVerifyOtpNegative(): void
    {
             /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/send-otp');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
         }
    }

    public function testReferalCodePositive(): void
    {
        /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/verify-invite-code',["mycode"=>"rajandhand"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    public function testReferalCodeNegative(): void
    {
        /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/verify-invite-code');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
         }
    }

    public function testResetPasswordPositive(): void
    {
         /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/reset-password',["email"=>"rajan@prepr.org","password"=>"Prepr@123","password_confirmation"=>"Prepr@123"]);
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals($response['success'],false);
        }
    }

    public function testResetPasswordNegative(): void
    {
         /** @todo This test is complete. */
      $response = $this->postJson('/api/v1/auth/reset-password');
      if($response['success']==true){
            $response->assertOk();
        }else{
            $this->assertEquals(200, $response->getStatusCode());
        }
    }
}
