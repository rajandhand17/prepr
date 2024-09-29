<?php

namespace App\Http\Controllers\Maestro\Dashboard;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Traits\Maestro\Dashboard\DashboardTrait;
use Exception;

class DashboardController extends Controller
{
    use DashboardTrait;

    /**
     * Display a listing of the dashboard components.
     */
    public function index()
    {
        try {
            $componentCount = $this->getComponentCount();
            if ($componentCount) {
                return view('maestro.dashboard.index', compact('componentCount'));
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('dashboard.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
