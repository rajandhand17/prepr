<?php

namespace App\Http\Controllers\Maestro\skill;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use App\Models\SkillGroup;
use App\Models\SkillStack;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\DB;
use App\Traits\Maestro\Skill\SkillTrait;
use App\Models\User;
use App\Traits\Maestro\Skill\SkillGroupTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SkillGroupController extends Controller
{
    use SkillGroupTrait;
    public function __construct()
    {
        $this->middleware('web');
    }
    public function index(Builder $builder, Request $request)
    {
        try {
            $groups = SkillGroup::orderBy('id', 'DESC');
        
            if (request()->ajax()) {
                return DataTables::eloquent($groups)
                ->addColumn('action', static function ($group) {
                    $html = '';
                    $html .= '<a href="' . route('skillgroup.show', ['skillgroup' => $group->id]) . '" class="mr-25 showUser" data-id="' . $group->id . '"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="' . route('skillgroup.edit', ['skillgroup' =>  $group->id]) . '" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="' . $group->id . '"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="javascript:void(0)" onclick="deleteSkillGroup(\'' . route('skillgroup.destroy', ['skillgroup' => $group->id]) . '\')"> <i class="fas fa-trash"></i></a>';
                    return $html;
                })
                ->editColumn('skill_stacks', static function ($group) {
                    $stacks = $group->skill_stacks;
                    $stack_names = [];
                    foreach ($stacks as $stack) {
                        if (SkillStack::where('id', $stack)->get()->count() > 0) {
                            $stack_names[] = SkillStack::find($stack)->title;
                        } else {
                            return "Stack doesn't exist";
                        }
                    }
                    return implode(', ', $stack_names);
                })
                ->editColumn('skills', static function ($group) {
                    $group_skills = $group->skills;
                    $group_skill_names = [];
                    foreach ($group_skills as $group_skill) {
                        if (Skill::where('id', $group_skill)->get()->count() > 0) {
                            $group_skill_names[] = Skill::find($group_skill)->title;
                        } else {
                            return "Skill doesn't exist";
                        }
                    }
                    return implode(', ', $group_skill_names);
                })
                ->toJson();
            }
            $languages = Language::where('status', 1)->get();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ];
            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName1 = 'title';
                    $columName2 = 'description';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName1 = $columName . '_title';
                    $columName2 = $columName.'_description';
                }
                $singleLangCol = ['data' => $columName1, 'name' => $columName1, 'title' => $single->name.' Group Title'];
                array_push($tableColumns, $singleLangCol);
                $singleLangCol = ['data' => $columName2, 'name' => $columName2, 'title' => $single->name.' Group Description'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'skill_stacks', 'name' => 'skill_stacks', 'title' => 'Group Stacks']);
            array_push($tableColumns, ['data' => 'skills', 'name' => 'skills', 'title' => 'Group Skills']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);


            $html = $builder->columns($tableColumns)->parameters(['order' => [0, 'desc']]);
            $languages = Language::where('status', 1)->get();
            return view('maestro.skillgroup.index', compact('html', 'languages'));
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('dashboard.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = Language::where('status', 1)->get();
            $skills = Skill::orderBy('id', 'DESC')->pluck('title', 'id')->take(50);
            //dd($skills);
            $selectedSkills = [];
            $stacks = SkillStack::orderBy('id', 'DESC')->pluck('title', 'id')->take(50);
            //dd($skills);
            $selectedStacks = [];
            return view('maestro.skillgroup.create', compact('languages', 'skills', 'selectedSkills', 'stacks', 'selectedStacks'));
        } catch (Exception $e) {
            return redirect()->route('skillgroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createSkillGroup($request)) {
                DB::commit();
                return redirect()->route('skillgroup.index')->with('success', 'Skill Group created successfully');
            }
            DB::rollback();
            return redirect()->route('skillgroup.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('skillgroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
           
            $skillgroup = $this->getSkillGroupById($id);
            $selectedSkills = [];
            foreach ( $skillgroup->skills as $skill) {
                $selectedSkills[] = $skill;
            }
            $languages = Language::where('status', 1)->get();
            if(!$skillgroup->exists){
                return redirect()->route('skillgroup.index')->with(['error' => 'Skill not found.']);
            }
            return view('maestro.skillgroup.view', compact('skillgroup', 'languages','selectedSkills'));
        } catch (Exception $e) {
            return redirect()->route('skillgroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = SkillGroup::find($id);
            $selectedSkills = [];
            foreach ( $data->skills as $skill) {
                $selectedSkills[] = $skill;
            }
        
            foreach ($data->skill_stacks as $skill_stack) {
                $selectedStacks[] = $skill_stack;
            }
            $title = $data->title;
            $description = $data->description;
            $skills = Skill::pluck('title', 'id');
            $stacks = SkillStack::pluck('title', 'id');
            $languages = Language::where('status', 1)->get();
            return view('maestro.skillgroup.edit', compact('skills', 'selectedSkills', 'title', 'description', 'languages', 'data', 'selectedStacks', 'stacks'));
        } catch (Exception $e) {
            dd($e);
            redirect()->route('skillgroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateSkillGroupById($id,$request)) {
                DB::commit();
                return redirect()->route('skillgroup.index')->with('success', 'Skill Group Updated successfully');
            }
            DB::rollback();
            return redirect()->route('skillgroup.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('skillgroup.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteSkillGroupById($id)) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
