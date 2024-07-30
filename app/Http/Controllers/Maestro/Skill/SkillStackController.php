<?php

namespace App\Http\Controllers\Maestro\skill;

use App\Helpers\Maestro\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\SkillService;
use App\Traits\Maestro\Skill\SkillStackTrait;
use Exception;
use Illuminate\Http\Request;
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
            $stacks = $this->getSkillStack();

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
            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ];
            foreach ($languages as $single) {
                $columName1 = UtilityHelper::getColumName($single->iso, 'title');
                $columName2 = UtilityHelper::getColumName($single->iso, 'description');
                $singleLangCol = ['data' => $columName1, 'name' => $columName1, 'title' => $single->name.' Stack Title'];
                array_push($tableColumns, $singleLangCol);
                $singleLangCol = ['data' => $columName2, 'name' => $columName2, 'title' => $single->name.' Stack Description'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'skills', 'name' => 'skills', 'title' => 'Stack Skills']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);

            $html = $builder->columns($tableColumns)->parameters(['order' => [0, 'desc']]);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.skillstack.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('dashboard.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $selectedSkills = [];

            return view('maestro.skillstack.create', compact('languages', 'selectedSkills'));
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
            if ($this->createSkillStack($request)) {
                return redirect()->route('skillstack.index')->with('success', 'Skill Stack created successfully');
            }

            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $skillstack = $this->getSkillStackById($id);
            $selectedSkills = [];
            foreach ($skillstack->skills as $skill) {
                $selectedSkills[] = $skill;
            }
            $languages = LanguageService::getAllActiveLanguages();
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
            $data = $this->getSkillStackById($id);
            $selectedSkills = SkillService::getSkillBasedOnIds($data->skills);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.skillstack.edit', compact('selectedSkills', 'languages', 'data'));
        } catch (Exception $e) {
            redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateSkillStackById($id, $request)) {
                return redirect()->route('skillstack.index')->with('success', 'Skill Stack Updated successfully');
            }

            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            return redirect()->route('skillstack.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteSkillStackById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

    public function getAjaxSkillStack(Request $request)
    {
        try {
            $response = $this->getAjaxAllSkillStack($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }
}
