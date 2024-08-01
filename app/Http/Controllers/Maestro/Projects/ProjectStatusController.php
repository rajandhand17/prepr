<?php

namespace App\Http\Controllers\Maestro\Projects;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\ProjectStatus;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\Project\ProjectStatusTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ProjectStatusController extends Controller
{
    use ProjectStatusTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder)
    {
        try {
            $projectStatus = $this->getProjectStatus();
            if (request()->ajax()) {
                return DataTables::eloquent($projectStatus)
                    ->addColumn('action', static function (ProjectStatus $projectStatusData) {
                        return '<a style="padding-left:20px" class="mr-10" href="'.route('projects-status.edit', ['projects_status' => $projectStatusData->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteProjectStatus(\''.route('projects-status.destroy', ['projects_status' => $projectStatusData->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('status', static function (ProjectStatus $projectStatusData) {
                        if ($projectStatusData->status == 0) {
                            return 'InActive';
                        } else {
                            return 'Active';
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
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Status Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.projects.status.index', compact('html', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('projects-status.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.projects.status.create', compact('languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('projects-status.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->storeUpdateProjectStatus($request, '', 'create')) {
                return redirect()->route('projects-status.index')->with(['success' => 'Project Stage Added successfully.']);
            }

            return redirect()->route('projects-status.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('projects-status.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $projectStatus = $this->findProjectStatus($id);

            return view('maestro.projects.status.edit', compact('projectStatus', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('projects-status.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->storeUpdateProjectStatus($request, $id, 'update')) {
                return redirect()->route('projects-status.index')->with(['success' => 'Project Status updated successfully.']);
            }

            return redirect()->route('projects-status.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('projects-status.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $projectStatus = $this->findProjectStatus($id);
            if (!empty($projectStatus)) {
                $this->deleteProjectStatus($projectStatus);

                return response()->json(['status' => 'success', 'message' => 'Project Status deleted successfully.']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
