<?php

namespace App\Http\Controllers\Web\Scorm;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ScormPlayerController extends Controller
{
    /**
     * @param string $scorm_uuid
     *
     * @return View
     */
    public function __invoke(string $scorm_uuid): View
    {
        return view('scorm.player', compact('scorm_uuid'));
    }
}
