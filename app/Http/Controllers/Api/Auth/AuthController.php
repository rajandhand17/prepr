<?php

namespace App\Http\Controllers\Api\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Requests\LoginFormRequest;
use App\Repositories\Api\Auth\AuthRepository;
use App\Http\Resources\Auth\RegisterUserResource;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends AppBaseController
{   
    
}
