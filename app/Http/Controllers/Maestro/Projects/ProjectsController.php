<?php

namespace App\Http\Controllers\Maestro\Projects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\DB;
use App\Traits\Maestro\Project\ProjectTrait;
use App\Models\Project;
use Exception;

class ProjectsController extends Controller
{
    use ProjectTrait;
    public function __construct()
    {
        $this->middleware('web');
    }
    public function index(Builder $builder)
    {
        try {
            $projects = $this->getProjectsList();
            if (request()->ajax()) {
                return DataTables::eloquent($projects)
                    ->addColumn('action', static function (Project $projects) {
                        return '<a style="padding-left:5px" class="mr-10" href="' . route('projects.show', ['project' => $projects->id]) . '"><i class="fas fa-eye"></i></a> <a style="padding-left:50px" class="mr-10" href="' . route('projects.edit', ['project' => $projects->id]) . '"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteProject(\'' . route('projects.destroy', ['project' => $projects->id]) . '\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('user_id', static function (Project $project) {
                        if ($project->user_id === 0 || $project->user_id === '') {
                            return 'Admin';
                        } else {
                            return $project->getUser->username ?? ' - ';
                        }
                    })
                    ->editColumn('privacy', static function (Project $project) {
                        if ($project->privacy == '0' || $project->privacy == '') {
                            return 'Public';
                        } elseif ($project->privacy == '1' ){
                            return 'Private';
                        }
                    })
                    ->editColumn('stage_id', static function (Project $project) {
                        if ($project->stage_id === 0 || $project->stage_id === '') {
                            return '-';
                        }
                        return $project->getStage->title ?? ' - ';
                    })
                    ->editColumn('status_id', static function (Project $project) {
                        if ($project->status_id === 0 || $project->status_id === "") {
                            return '-';
                        }
                        return $project->getStatus->title ?? ' - ';
                    })
                    ->editColumn('category_id', function (Project $project) {
                        if ($project->category_id === 0 || $project->category_id === '') {
                            return '-';
                        }
                        return $project->getCategory->title ?? ' - ';
                    })
                    ->toJson();
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
                ['data' => 'title', 'name' => 'title', 'title' => 'Project Title'],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => 'User Name'],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy'],
                ['data' => 'category_id', 'name' => 'category_id', 'title' => 'Category'],
                ['data' => 'stage_id', 'name' => 'stage_id', 'title' => 'Stage'],
                ['data' => 'status_id', 'name' => 'status_id', 'title' => 'Project Status'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '15%'],
            ]);
            $module_name = 'Project';
            return view('maestro.projects.project.index', compact('html','module_name'));
        } catch (Exception $e) {
            return response()->route('projects.index')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $project_user   = $this->getProjectAssociateItems('user');
            $project_stage  = $this->getProjectAssociateItems('stage');
            $project_type   = $this->getProjectAssociateItems('type');
            $project_status = $this->getProjectAssociateItems('status');
            $project_industry   = $this->getProjectAssociateItems('industry');
            $project_verticals  = $this->getProjectAssociateItems('vertical');
            $project_category   = $this->getProjectAssociateItems('category');
            $project_privacy    = $this->getProjectAssociateItems('privacy');
            $project_team   = $this->getProjectAssociateItems('team');
            $project_lab    = $this->getProjectAssociateItems('lab');
            $project_challenge  = $this->getProjectAssociateItems('challenge');
            return view('maestro.projects.project.create', compact('project_user','project_stage','project_type','project_status','project_industry','project_verticals','project_category','project_privacy','project_team','project_lab','project_challenge'));
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createProject($request)) {
                DB::commit();
                return redirect()->route('projects.index')->with('success', 'Project created successfully');
            }
            DB::rollback();
            return redirect()->route('projects.index')->with(['error' => 'Something want wrong.']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('projects.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $project = $this->getProjectById($id);
            if(!$project->exists){
                return redirect()->route('projects.index')->with(['error' => 'Project not found.']);
            }
            return view('maestro.projects.project.view', compact('project'));
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $project = $this->getProjectById($id);
            if(!$project->exists){
                return redirect()->route('projects.index')->with(['error' => 'Project not found.']);
            }
            $selected_member = [];
            $project_user   = $this->getProjectAssociateItems('user');
            $project_stage  = $this->getProjectAssociateItems('stage');
            $project_type   = $this->getProjectAssociateItems('type');
            $project_status = $this->getProjectAssociateItems('status');
            $project_industry   = $this->getProjectAssociateItems('industry');
            $project_verticals  = $this->getProjectAssociateItems('vertical');
            $project_category   = $this->getProjectAssociateItems('category');
            $project_privacy    = $this->getProjectAssociateItems('privacy');
            $project_team   = $this->getProjectAssociateItems('team');
            $project_lab    = $this->getProjectAssociateItems('lab');
            $project_challenge  = $this->getProjectAssociateItems('challenge');
            return view('maestro.projects.project.edit', compact('project','selected_member','project_user','project_stage','project_type','project_status','project_industry','project_verticals','project_category','project_privacy','project_team','project_lab','project_challenge'));
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateProjectById($id,$request)) {
                DB::commit();
                return redirect()->route('projects.index')->with('success', 'Project Updated successfully');
            }
            DB::rollback();
            return redirect()->route('projects.index')->with(['error' => 'Something want wrong']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('projects.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteProjectById($id)) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Project deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => 'Something want wrong.']);
        }
    }
}
