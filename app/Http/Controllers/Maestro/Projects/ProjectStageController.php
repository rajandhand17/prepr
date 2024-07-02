<?php

namespace App\Http\Controllers\Maestro\Projects;

use App\Http\Controllers\Controller;
use App\Models\ProjectStage;
use App\Traits\Maestro\Project\ProjectStageTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ProjectStageController extends Controller
{
    use ProjectStageTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder)
    {
        try {
            $projectStage = $this->getProjectStage();
            if (request()->ajax()) {
                return DataTables::eloquent($projectStage)
                    ->addColumn('action', static function (ProjectStage $stage) {
                        return '<a style="padding-left:20px" class="mr-10" href="'.route('projects-stage.edit', ['projects_stage' => $stage->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteProjectStage(\''.route('projects-stage.destroy', ['projects_stage' => $stage->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('status', static function (ProjectStage $stage) {
                        if ($stage->status == 0) {
                            return 'Not Active';
                        } else {
                            return 'Active';
                        }
                    })
                    ->addIndexColumn()
                    ->toJson();
            }

            $languages = $this->getLanguage();
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
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Stage Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.projects.stage.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('projects-stage.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->getLanguage();
            $status = $this->getProjectStageStatus();

            return view('maestro.projects.stage.create', compact('languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('projects-stage.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateProjectStage($request, '', 'create')) {
                DB::commit();

                return redirect()->route('projects-stage.index')->with(['success' => 'Project Stage Added successfully.']);
            }

            return redirect()->route('projects-stage.index')->with(['error' => 'Something want wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('projects-stage.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->getLanguage();
            $projectStage = $this->findProjectStage($id);
            $status = $this->getProjectStageStatus();

            return view('maestro.projects.stage.edit', compact('projectStage', 'languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('projects-stage.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateProjectStage($request, $id, 'update')) {
                DB::commit();

                return redirect()->route('projects-stage.index')->with(['success' => 'Project Stage updated successfully.']);
            }

            return redirect()->route('projects-stage.index')->with(['error' => 'Something want wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('projects-stage.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $projectStage = $this->findProjectStage($id);
            if (!empty($projectStage)) {
                $this->deleteProjectStage($projectStage);
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Project Stage deleted successfully.']);
            }
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Something want wrong.']);
        }
    }
}
