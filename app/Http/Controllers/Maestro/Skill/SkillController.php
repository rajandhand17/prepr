<?php

namespace App\Http\Controllers\Maestro\skill;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use App\Traits\Maestro\Skill\SkillTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $skills = Skill::orderBy('id', 'DESC');
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
            $languages = Language::where('status', 1)->get();
            $tableColumns = [
                ['data' => 'id', 'name' => '', 'title' => 'id', 'orderable' => false, 'searchable' => false],
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
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Skill Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);
            $html = $builder->columns($tableColumns);
            view()->share('module_name', 'Challenge');
            $languages = Language::where('status', 1)->get();

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
            $languages = Language::where('status', 1)->get();

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
            DB::beginTransaction();
            if ($this->createSkill($request)) {
                DB::commit();

                return redirect()->route('skills.index')->with('success', 'Skill created successfully');
            }
            DB::rollback();

            return redirect()->route('skills.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

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
            $languages = Language::where('status', 1)->get();
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
            $data = Skill::find($id);
            $languages = Language::where('status', 1)->get();

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
            DB::beginTransaction();
            if ($this->updateSkillById($id, $request)) {
                DB::commit();

                return redirect()->route('skills.index')->with('success', 'Skill Updated successfully');
            }
            DB::rollback();

            return redirect()->route('skills.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('skills.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteSkillById($id)) {
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
