<?php

namespace App\Http\Controllers\Maestro\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Traits\Maestro\Project\ProjectTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ProjectsController extends Controller
{
    use ProjectTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder)
    {
        try {
            $projects = $this->getProjectsList();
            if (request()->ajax()) {
                return DataTables::eloquent($projects)
                    ->addColumn('action', static function (Project $projects) {
                        return '<a style="padding-left:5px" class="mr-10" href="'.route('projects.show', ['project' => $projects->id]).'"><i class="fas fa-eye"></i></a> <a style="padding-left:50px" class="mr-10" href="'.route('projects.edit', ['project' => $projects->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteProject(\''.route('projects.destroy', ['project' => $projects->id]).'\')"><i class="fas fa-trash"></i></a>';
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
                        } elseif ($project->privacy == '1') {
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
                        if ($project->status_id === 0 || $project->status_id === '') {
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

            return view('maestro.projects.project.index', compact('html', 'module_name'));
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
            $projectData = $this->getProjectAssociateItems('create', null);

            return view('maestro.projects.project.create', compact('projectData'));
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createProject($request)) {
                return redirect()->route('projects.index')->with('success', 'Project created successfully');
            }

            return redirect()->route('projects.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $project = $this->getProjectById($id);
            if (!$project->exists) {
                return redirect()->route('projects.index')->with(['error' => 'Project not found.']);
            }

            return view('maestro.projects.project.view', compact('project'));
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $project = $this->getProjectById($id);
            if (!$project->exists) {
                return redirect()->route('projects.index')->with(['error' => 'Project not found.']);
            }
            $projectData = $this->getProjectAssociateItems('edit', $project);

            return view('maestro.projects.project.edit', compact('project', 'projectData'));
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateProjectById($id, $request)) {
                return redirect()->route('projects.index')->with('success', 'Project Updated successfully');
            }

            return redirect()->route('projects.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('projects.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteProjectById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Project deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
