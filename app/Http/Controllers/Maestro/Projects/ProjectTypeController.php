<?php

namespace App\Http\Controllers\Maestro\Projects;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\Project\ProjectTypeTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ProjectTypeController extends Controller
{
    use ProjectTypeTrait;

    public function index(Builder $builder)
    {
        try {
            $projectType = $this->getProjectType();
            if (request()->ajax()) {
                return DataTables::eloquent($projectType)
                    ->addColumn('action', static function (ProjectType $type) {
                        return '<a style="padding-left:20px" class="mr-10" href="'.route('projects-type.edit', ['projects_type' => $type->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteProjectType(\''.route('projects-type.destroy', ['projects_type' => $type->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('status', static function (ProjectType $type) {
                        if ($type->status == 0) {
                            return 'Not Active';
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
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Type Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.projects.type.index', compact('html', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('projects-type.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.projects.type.create', compact('languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('projects-type.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->storeUpdateProjectType($request, '', 'create')) {
                return redirect()->route('projects-type.index')->with(['success' => 'Project type Added successfully.']);
            }

            return redirect()->route('projects-type.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('projects-type.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $projectType = $this->findProjectType($id);

            return view('maestro.projects.type.edit', compact('projectType', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('projects-type.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->storeUpdateProjectType($request, $id, 'update')) {
                return redirect()->route('projects-type.index')->with(['success' => 'Project type updated successfully.']);
            }

            return redirect()->route('projects-type.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('projects-type.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $ProjectType = $this->findProjectType($id);
            if (!empty($ProjectType)) {
                $this->deleteProjectType($ProjectType);

                return response()->json(['status' => 'success', 'message' => 'Project type deleted successfully.']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
