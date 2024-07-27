<?php

namespace App\Http\Controllers\Maestro\Projects;

use App\Helpers\Maestro\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\ProjectVertical;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\Project\ProjectVerticalTrait;
use Exception;
use Illuminate\Http\Request;
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
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Vertical Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.projects.vertical.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.projects.vertical.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->storeUpdateProjectVertical($request, '', 'create')) {
                return redirect()->route('projects-vertical.index')->with(['success' => 'Project Vertical Added successfully.']);
            }

            return redirect()->route('projects-vertical.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $projectVertical = $this->findProjectVertical($id);

            return view('maestro.projects.vertical.edit', compact('projectVertical', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->storeUpdateProjectVertical($request, $id, 'update')) {
                return redirect()->route('projects-vertical.index')->with(['success' => 'Project Vertical updated successfully.']);
            }

            return redirect()->route('projects-vertical.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('projects-vertical.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $projectVertical = $this->findProjectVertical($id);
            if (!empty($projectVertical)) {
                $this->deleteProjectVertical($projectVertical);

                return response()->json(['status' => 'success', 'message' => 'Project Vertical deleted successfully.']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
