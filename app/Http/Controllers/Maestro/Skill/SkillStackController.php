<?php

namespace App\Http\Controllers\Maestro\skill;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use App\Models\SkillStack;
use App\Traits\Maestro\Skill\SkillStackTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class SkillStackController extends Controller
{
    use SkillStackTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $stacks = SkillStack::orderBy('id', 'DESC');

            if (request()->ajax()) {
                return DataTables::eloquent($stacks)
                ->addColumn('action', static function ($stack) {
                    $html = '';
                    $html .= '<a href="'.route('skillstack.show', ['skillstack' => $stack->id]).'" class="mr-25 showUser" data-id="'.$stack->id.'"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="'.route('skillstack.edit', ['skillstack' =>  $stack->id]).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$stack->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="javascript:void(0)" onclick="deleteSkillStack(\''.route('skillstack.destroy', ['skillstack' => $stack->id]).'\')"> <i class="fas fa-trash"></i></a>';

                    return $html;
                })
                ->editColumn('skills', static function ($stack) {
                    $stack_skills = $stack->skills;
                    $stack_skill_names = [];
                    foreach ($stack_skills as $stack_skill) {
                        if (Skill::where('id', $stack_skill)->get()->count() > 0) {
                            $stack_skill_names[] = Skill::find($stack_skill)->title;
                        } else {
                            return "Skill doesn't exist";
                        }
                    }

                    return implode(', ', $stack_skill_names);
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
                    $columName1 = $columName.'_title';
                    $columName2 = $columName.'_description';
                }
                $singleLangCol = ['data' => $columName1, 'name' => $columName1, 'title' => $single->name.' Stack Title'];
                array_push($tableColumns, $singleLangCol);
                $singleLangCol = ['data' => $columName2, 'name' => $columName2, 'title' => $single->name.' Stack Description'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'skills', 'name' => 'skills', 'title' => 'Stack Skills']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);

            $html = $builder->columns($tableColumns)->parameters(['order' => [0, 'desc']]);
            $languages = Language::where('status', 1)->get();

            return view('maestro.skillstack.index', compact('html', 'languages'));
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

            return view('maestro.skillstack.create', compact('languages', 'skills', 'selectedSkills'));
        } catch (Exception $e) {
            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createSkillStack($request)) {
                DB::commit();

                return redirect()->route('skillstack.index')->with('success', 'Skill Stack created successfully');
            }
            DB::rollback();

            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $skillstack = $this->getSkillSTackById($id);
            $selectedSkills = [];
            foreach ($skillstack->skills as $skill) {
                $selectedSkills[] = $skill;
            }
            $languages = Language::where('status', 1)->get();
            if (!$skillstack->exists) {
                return redirect()->route('skillstack.index')->with(['error' => 'Skill not found.']);
            }

            return view('maestro.skillstack.view', compact('skillstack', 'languages', 'selectedSkills'));
        } catch (Exception $e) {
            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = SkillStack::find($id);
            $selectedSkills = [];
            foreach ($data->skills as $skill) {
                $selectedSkills[] = $skill;
            }
            $title = $data->title;
            $description = $data->description;
            $skills = Skill::whereIn('id', $selectedSkills)->pluck('title', 'id');

            $languages = Language::where('status', 1)->get();

            return view('maestro.skillstack.edit', compact('skills', 'selectedSkills', 'title', 'description', 'languages', 'data'));
        } catch (Exception $e) {
            dd($e);
            redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateSkillStackById($id, $request)) {
                DB::commit();

                return redirect()->route('skillstack.index')->with('success', 'Skill Stack Updated successfully');
            }
            DB::rollback();

            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteSkillStackById($id)) {
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
