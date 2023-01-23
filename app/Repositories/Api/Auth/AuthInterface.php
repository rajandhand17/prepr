<?php

namespace App\Repositories\Api\Auth;

Interface AuthInterface{
  public function login($request);
  public function register($request);
  public function checkUsername($request);
  public function checkEmail($request);
  public function checkPhone($request);
  public function checkOrgnization($request);
  public function sendOtp($request);
  public function verifyOtp($request);
  public function referenceCode($request);
}

?>