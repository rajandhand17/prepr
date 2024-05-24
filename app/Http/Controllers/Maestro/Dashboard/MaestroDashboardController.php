<?php

namespace App\Http\Controllers\Maestro\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;

class MaestroDashboardController extends AppBaseController
{
    public function __construct()
    {
        $this->middleware('web');
    }
    /**
     * Display a listing of the dashboard components.
     */
    public function index()
    {
        return view('maestro.dashboard.dashboard');
    }
}
