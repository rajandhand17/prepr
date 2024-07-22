<?php

namespace App\Http\Controllers\Maestro\Projects;

use App\Http\Controllers\Controller;
use App\Models\ProjectSubmissionRequirement;
use App\Traits\Maestro\Project\ProjectSubmissionRequirementTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use App\Services\Maestro\LanguageService;

class ProjectSubmissionRequirementController extends Controller
{
    use ProjectSubmissionRequirementTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder)
    {
        try {
            $submissionRequirement = $this->getSubmissionRequirement();
            if (request()->ajax()) {
                return DataTables::eloquent($submissionRequirement)
                    ->addColumn('action', static function (ProjectSubmissionRequirement $projectSubmissionRequirement) {
                        return '<a style="padding-left:20px" class="mr-10" href="'.route('projects-submission-requirement.edit', ['projects_submission_requirement' => $projectSubmissionRequirement->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteSubmissionRequirement(\''.route('projects-submission-requirement.destroy', ['projects_submission_requirement' => $projectSubmissionRequirement->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('status', static function (ProjectSubmissionRequirement $projectSubmissionRequirement) {
                        if ($projectSubmissionRequirement->status == 1) {
                            return 'Active';
                        } else {
                            return 'Not Active';
                        }
                    })
                    ->addIndexColumn()
                    ->toJson();
            }

            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false],
            ];
            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName = 'title';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName = $columName.'_title';
                }
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Project Submission Requirement Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.projects.submissionrequirement.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('projects-submission-requirement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $status = $this->getSubmissionRequirementStatus();

            return view('maestro.projects.submissionrequirement.create', compact('languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('projects-submission-requirement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateSubmissionRequirement($request, '', 'create')) {
                DB::commit();

                return redirect()->route('projects-submission-requirement.index')->with(['success' => 'Project Submission Requirement Added successfully.']);
            }

            return redirect()->route('projects-submission-requirement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('projects-submission-requirement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $submissionRequirement = $this->findSubmissionRequirement($id);
            $status = $this->getSubmissionRequirementStatus();

            return view('maestro.projects.submissionrequirement.edit', compact('submissionRequirement', 'languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('projects-submission-requirement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateSubmissionRequirement($request, $id, 'update')) {
                DB::commit();

                return redirect()->route('projects-submission-requirement.index')->with(['success' => 'Project Submission Requirement updated successfully.']);
            }

            return redirect()->route('projects-submission-requirement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('projects-submission-requirement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $submissionRequirement = $this->findSubmissionRequirement($id);
            if (!empty($submissionRequirement)) {
                $this->deleteSubmissionRequirement($submissionRequirement);
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Project Submission Requirement deleted successfully.']);
            }
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
