<?php

namespace App\Repositories\Api\Auth;

use PhpParser\Builder\Interface_;

Interface AuthInterface{
  public function register($request);
  public function checkUsername($request);
  public function checkEmail($request);
  public function checkPhone($request);
  public function checkOrgnization($request);
  public function sendOtp($request);
  public function verifyOtp($request);
}

?>