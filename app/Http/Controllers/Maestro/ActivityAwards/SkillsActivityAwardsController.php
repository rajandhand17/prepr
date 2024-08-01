<?php

namespace App\Http\Controllers\Maestro\ActivityAwards;

use App\Http\Controllers\Controller;
use App\Models\SkillsActivityAward;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\SkillService;
use App\Traits\Maestro\SkillsActivityAward\SkillsActivityAwardTrait;
use Exception;
use Illuminate\Http\Request;
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
            $skills_activity_awards = $this->getSkillsActivityAward();
            if (request()->ajax()) {
                return DataTables::eloquent($skills_activity_awards)
                    ->editColumn('name', static function (SkillsActivityAward $award) {
                        return $award->name;
                    })
                    ->editColumn('skill', static function (SkillsActivityAward $award) {
                        return SkillService::getSelectedSkillsNameByIds(json_decode($award->skill, true));
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
            $selectedSkills = [];
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.activityawards.skillsAwards.create', compact('selectedSkills', 'languages'));
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
            if ($this->createSkillsActivityAward($request)) {
                return redirect()->route('skillsaward.index')->with('success', 'Activity Award has been created successfully');
            }

            return redirect()->route('skillsaward.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
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
            $selectedSkills = SkillService::getSkillBasedOnIds(json_decode($award->skill, true));
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.activityAwards.skillsAwards.edit', compact('award', 'selectedSkills', 'languages'));
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
            if ($this->updateSkillsActivityAwardById($id, $request)) {
                return redirect()->route('skillsaward.index')->with('success', 'Activity Award has been Updated successfully');
            }

            return redirect()->route('skillsaward.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
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
            if ($this->deleteSkillsActivityAwardById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
