<?php

namespace App\Http\Controllers\Maestro\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;

class MaestroLoginController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('maestro.auth.login');
    }
}
