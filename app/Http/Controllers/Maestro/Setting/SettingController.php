<?php

namespace App\Http\Controllers\Maestro\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Request $request)
    {
        try {

        }catch (\Exception $e) {
            return redirect()->route('setting.index')->with(['error' => 'Something want wrong.']);
        }
    }

}
