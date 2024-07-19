<?php

namespace App\Http\Controllers\Maestro\ActivityAwards;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Skill;
use App\Models\SkillsActivityAward;
use App\Traits\Maestro\SkillsActivityAward\SkillsActivityAwardTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class SkillsActivityAwardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use SkillsActivityAwardTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $skills_activity_awards = SkillsActivityAward::orderBy('id', 'DESC');
            if (request()->ajax()) {
                return DataTables::eloquent($skills_activity_awards)
                    ->editColumn('name', static function (SkillsActivityAward $award) {
                        return $award->name;
                    })
                    ->editColumn('skill', static function (SkillsActivityAward $award) {
                        return Skill::where('id', $award->skill)->first()->skill;
                    })
                    ->editColumn('image', static function (SkillsActivityAward $award) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('front/img/no-img.jpg').'";';

                        return "<img src ='".asset($award->image)."' width='60' ".$onerror.'>';
                    })->rawColumns(['image', 'action'])
                    ->addColumn('action', static function (SkillsActivityAward $award) {
                        return '<a href="'.route('skillsaward.edit', $award->id).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$award->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="deleteSkillsAward(\''.route('skillsaward.destroy', $award->id).'\')"> <i class="fas fa-trash"></i></a>';
                    })
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
                ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
                ['data' => 'skill', 'name' => 'skill', 'title' => 'Skill'],
                ['data' => 'image', 'name' => 'image', 'title' => 'Image'],
                ['data' => 'points', 'name' => 'points', 'title' => 'Points'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
            ]);

            return view('maestro.activityawards.skillsAwards.index', compact('html'));
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $selectedSkill = [];
            $skills = Skill::pluck('title', 'id')->take(20);
            $languages = Language::where('status', 1)->get();

            return view('maestro.activityawards.skillsAwards.create', compact('skills', 'selectedSkill', 'languages'));
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createSkillsActivityAward($request)) {
                DB::commit();

                return redirect()->route('skillsaward.index')->with('success', 'Activity Award has been created successfully');
            }
            DB::rollback();

            return redirect()->route('skillsaward.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('skillsaward.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $award = SkillsActivityAward::find($id);
            $selectedSkill = [$award->skill];
            $skills = Skill::pluck('title', 'id')->take(20);
            $languages = Language::where('status', 1)->get();

            return view('maestro.activityAwards.skillsAwards.edit', compact('award', 'selectedSkill', 'skills', 'languages'));
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status'  => 'error',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateSkillsActivityAwardById($id, $request)) {
                DB::commit();

                return redirect()->route('skillsaward.index')->with('success', 'Activity Award has been Updated successfully');
            }
            DB::rollback();

            return redirect()->route('skillsaward.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('skillsaward.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteSkillsActivityAwardById($id)) {
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
