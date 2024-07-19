<?php

namespace App\Http\Controllers\Maestro\Projects;

use App\Http\Controllers\Controller;
use App\Models\ProjectVertical;
use App\Traits\Maestro\Project\ProjectVerticalTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ProjectVerticalController extends Controller
{
    use ProjectVerticalTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder)
    {
        try {
            $projectVertical = $this->getProjectVertical();
            if (request()->ajax()) {
                return DataTables::eloquent($projectVertical)
                    ->addColumn('action', static function (ProjectVertical $vertical) {
                        return '<a style="padding-left:20px" class="mr-10" href="'.route('projects-vertical.edit', ['projects_vertical' => $vertical->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteProjectVertical(\''.route('projects-vertical.destroy', ['projects_vertical' => $vertical->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('status', static function (ProjectVertical $vertical) {
                        if ($vertical->status == 0) {
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
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Vertical Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.projects.vertical.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->getLanguage();
            $status = $this->getProjectVerticalStatus();

            return view('maestro.projects.vertical.create', compact('languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateProjectVertical($request, '', 'create')) {
                DB::commit();

                return redirect()->route('projects-vertical.index')->with(['success' => 'Project Vertical Added successfully.']);
            }

            return redirect()->route('projects-vertical.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('projects-vertical.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->getLanguage();
            $projectVertical = $this->findProjectVertical($id);
            $status = $this->getProjectVerticalStatus();

            return view('maestro.projects.vertical.edit', compact('projectVertical', 'languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateProjectVertical($request, $id, 'update')) {
                DB::commit();

                return redirect()->route('projects-vertical.index')->with(['success' => 'Project Vertical updated successfully.']);
            }

            return redirect()->route('projects-vertical.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('projects-vertical.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $projectVertical = $this->findProjectVertical($id);
            if (!empty($projectVertical)) {
                $this->deleteProjectVertical($projectVertical);
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Project Vertical deleted successfully.']);
            }
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
