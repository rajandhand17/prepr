<?php

namespace App\Http\Controllers\Maestro\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
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
        return view('maestro.dashboard.index');
    }
}
