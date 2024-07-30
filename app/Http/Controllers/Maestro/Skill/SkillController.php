<?php

namespace App\Http\Controllers\Maestro\skill;

use App\Helpers\Maestro\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\Skill\SkillTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class SkillController extends Controller
{
    use SkillTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $skills = $this->getSkills();
            if (request()->ajax()) {
                $i = 1;

                return DataTables::eloquent($skills)
                    ->addIndexColumn()
                    ->addColumn('action', static function (Skill $skills) {
                        $html = '';
                        $html .= '<a href="'.route('skills.show', ['skill' => $skills->id]).'" class="mr-25 showUser" data-id="'.$skills->id.'"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;';
                        $html .= '<a href="'.route('skills.edit', ['skill' => $skills->id]).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$skills->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;';
                        $html .= '<a href="javascript:void(0)" onclick="deleteSkill(\''.route('skills.destroy', ['skill' => $skills->id]).'\')"> <i class="fas fa-trash"></i></a>';

                        return $html;
                    })
                    ->editColumn('id', function (Skill $skill) {
                        if ($skill->id === 0 || $skill->id === '') {
                            return 'Admin';
                        } else {
                            return $skill->id ?? ' - ';
                        }
                    })
                    ->toJson();
            }
            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => '', 'title' => 'id', 'orderable' => false, 'searchable' => false],
            ];
            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Skill Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);
            $html = $builder->columns($tableColumns);

            return view('maestro.skills.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.skills.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->route('skills.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createSkill($request)) {
                return redirect()->route('skills.index')->with('success', 'Skill created successfully');
            }

            return redirect()->route('skills.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            return redirect()->route('users.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $skill = $this->getSkillById($id);
            $languages = LanguageService::getAllActiveLanguages();
            if (!$skill->exists) {
                return redirect()->route('skills.index')->with(['error' => 'Skill not found.']);
            }

            return view('maestro.skills.view', compact('skill', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('skills.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $data = $this->getSkillById($id);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.skills.edit', compact('data', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('skills.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateSkillById($id, $request)) {
                return redirect()->route('skills.index')->with('success', 'Skill Updated successfully');
            }

            return redirect()->route('skills.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            return redirect()->route('skills.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteSkillById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

    public function getAjaxSkills(Request $request)
    {
        try {
            $response = $this->getAjaxAllSkills($request);
            if ($response) {
                return $response;
            }

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.', 'result' => [], 'more' => false, 'total_count' => 0]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.', 'result' => [], 'more' => false, 'total_count' => 0]);
        }
    }
}
