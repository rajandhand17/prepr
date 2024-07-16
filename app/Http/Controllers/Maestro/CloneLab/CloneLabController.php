<?php

namespace App\Http\Controllers\Maestro\CloneLab;

use App\Http\Controllers\Controller;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\OrganizationService;
use App\Traits\Maestro\CloneLab\CloneLabTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

class CloneLabController extends Controller
{
    use CloneLabTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $organizations = OrganizationService::getOrganizations();
            $associativeLab = $this->getAllLabs();
            $languages = LanguageService::getLanguages();

            return view('maestro.cloneLab.index', compact('organizations', 'languages', 'associativeLab'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function store(Request $request)
    {
        try {
            $getResponses = $this->createLab($request);
            if ($getResponses !== false) {
                return redirect()->route('clone-lab.index')->with('success', 'Lab created successfully');
            }

            return redirect()->route('clone-lab.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (\Exception $e) {
            return redirect()->route('dashboard.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
