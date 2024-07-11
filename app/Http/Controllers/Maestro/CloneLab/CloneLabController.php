<?php

namespace App\Http\Controllers\Maestro\CloneLab;

use App\Http\Controllers\Controller;
use App\Services\Maestro\Lab\LabService;
use App\Services\Maestro\LabAchievement\LabAchievementService;
use App\Services\Maestro\LabAddress\LabAddressService;
use App\Services\Maestro\LabExternalLinks\LabExternalLinksService;
use App\Services\Maestro\LabSkillsGroupsStack\LabSkillsGroupsStackService;
use App\Services\Maestro\LabTagsGroups\LabTagsGroupsService;
use App\Services\Maestro\Language\LanguageService;
use App\Services\Maestro\Organization\OrganizationService;
use App\Traits\Maestro\CloneLab\CloneLabTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

class CloneLabController extends Controller
{
    use CloneLabTrait;
    public function __construct(LabAchievementService $labAchievementService, LabExternalLinksService $labExternalLinksService,LabTagsGroupsService $labTagsGroupsService,LabSkillsGroupsStackService $labSkillsGroupsStackService,LabAddressService $labAddressService,LabService $labService,OrganizationService $organizationService,LanguageService $languageService)
    {
        $this->middleware('web');
        $this->labService = $labService;
        $this->organizationService = $organizationService;
        $this->languageService = $languageService;
        $this->labAddressService=$labAddressService;
        $this->labSkillsGroupsStackService=$labSkillsGroupsStackService;
        $this->labTagsGroupsService=$labTagsGroupsService;
        $this->labExternalLinksService=$labExternalLinksService;
        $this->labAchievementService=$labAchievementService;

    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $organizations=OrganizationService::getOrganizations();
            $associativeLab=$this->getAllLabs();
            $languages =LanguageService::getLanguages();
            return view('maestro.cloneLab.index', compact('organizations','languages','associativeLab'));
        }catch (\Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function store(Request $request)
    {
        try {
            $getResponses=$this->createLab($request);
            if ($getResponses!==false) {
                return redirect()->route('clone-lab.index')->with('success', 'Lab created successfully');
            }
            return redirect()->route('clone-lab.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (\Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
