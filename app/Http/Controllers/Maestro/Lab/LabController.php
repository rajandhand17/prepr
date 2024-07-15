<?php

namespace App\Http\Controllers\Maestro\Lab;

use App\Http\Controllers\Controller;
use App\Services\Maestro\LabService;
use App\Traits\Maestro\Lab\LabTrait;
use Illuminate\Http\Request;

class LabController extends Controller
{
    use LabTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function getLabsBasedOnOrganization(Request $request)
    {
        try {
            $getList = $this->getLabsBasedOnOrganizations($request);
            if ($getList) {
                return $getList;
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }
}
