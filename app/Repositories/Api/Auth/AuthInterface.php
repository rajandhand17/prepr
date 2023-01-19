<?php

namespace App\Repositories\Api\Auth;

use PhpParser\Builder\Interface_;

Interface AuthInterface{
  public function register($request);
  public function checkusername($request);
  public function checkemail($request);
  public function checkphone($request);
  public function checkorgnization($request);
}

?>