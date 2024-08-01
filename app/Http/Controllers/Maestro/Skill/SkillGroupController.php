<?php

namespace App\Http\Controllers\Maestro\skill;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\SkillStack;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\SkillService;
use App\Services\Maestro\SkillStackService;
use App\Traits\Maestro\Skill\SkillGroupTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

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
            $groups = $this->getSkillGroup();

            if (request()->ajax()) {
                return DataTables::eloquent($groups)
                ->addColumn('action', static function ($group) {
                    $html = '';
                    $html .= '<a href="'.route('skill-group.show', ['skillgroup' => $group->id]).'" class="mr-25 showUser" data-id="'.$group->id.'"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="'.route('skill-group.edit', ['skillgroup' =>  $group->id]).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$group->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                    $html .= '<a href="javascript:void(0)" onclick="deleteSkillGroup(\''.route('skillgroup.destroy', ['skill-group' => $group->id]).'\')"> <i class="fas fa-trash"></i></a>';

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
            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ];
            foreach ($languages as $single) {
                $columName1 = UtilityHelper::getColumName($single->iso, 'title');
                $columName2 = UtilityHelper::getColumName($single->iso, 'description');
                $singleLangCol = ['data' => $columName1, 'name' => $columName1, 'title' => $single->name.' Group Title'];
                array_push($tableColumns, $singleLangCol);
                $singleLangCol = ['data' => $columName2, 'name' => $columName2, 'title' => $single->name.' Group Description'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'skill_stacks', 'name' => 'skill_stacks', 'title' => 'Group Stacks']);
            array_push($tableColumns, ['data' => 'skills', 'name' => 'skills', 'title' => 'Group Skills']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);

            $html = $builder->columns($tableColumns)->parameters(['order' => [0, 'desc']]);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.skill-group.index', compact('html', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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
            $selectedSkills = $selectedStacks = [];

            return view('maestro.skill-group.create', compact('languages', 'selectedSkills', 'selectedStacks'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('skill-group.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createSkillGroup($request)) {
                return redirect()->route('skill-group.index')->with('success', 'Skill Group created successfully');
            }

            return redirect()->route('skill-group.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('skill-group.index')->with(['error' => 'Something went wrong.']);
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
            foreach ($skillgroup->skills as $skill) {
                $selectedSkills[] = $skill;
            }
            $languages = LanguageService::getAllActiveLanguages();
            if (!$skillgroup->exists) {
                return redirect()->route('skill-group.index')->with(['error' => 'Skill not found.']);
            }

            return view('maestro.skill-group.view', compact('skillgroup', 'languages', 'selectedSkills'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('skill-group.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = $this->getSkillGroupById($id);
            $selectedSkills = SkillService::getSkillBasedOnIds($data->skills);
            $selectedStacks = SkillStackService::getSkillStackBasedOnIds($data->skill_stacks);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.skill-group.edit', compact('selectedSkills', 'languages', 'data', 'selectedStacks'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            redirect()->route('skill-group.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateSkillGroupById($id, $request)) {
                return redirect()->route('skill-group.index')->with('success', 'Skill Group Updated successfully');
            }

            return redirect()->route('skill-group.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return redirect()->route('skill-group.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteSkillGroupById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
