<?php

namespace App\Http\Controllers\Maestro\Challenges;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Maestro\Challenge\ChallengeTrait;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\ChallengeAssessmentService;
use App\Services\Maestro\ChallengeAssessmentCriteriaService;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use App\Models\Challenge;
use Exception;

class ChallengeController extends Controller
{
    use ChallengeTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            $challenges = $this->getChallengeList();
            if (request()->ajax()) {
                return DataTables::eloquent($challenges)
                    ->addIndexColumn()
                    ->editColumn('title', static function (Challenge $challenges) {
                        return $challenges->title;
                    })
                    ->editColumn('user_id', static function (Challenge $challenge) {
                        if (!empty($challenge->user_id)) {
                            return $challenge->creator->username ?? ' - ';
                        } else {
                            return ' - ';
                        }
                    })

                    ->editColumn('status', static function (Challenge $challenges) {
                        if ($challenges->status == '0') {
                            $html = "<span class='badge badge-info'>Draft</span>";
                        } elseif ($challenges->status == '1') {
                            $html = "<span class='badge badge-success'>Published</span>";
                        } elseif ($challenges->status == '2') {
                            $html = "<span class='badge badge-danger'>Archive</span>";
                        }

                        return $html;
                    })
                    ->editColumn('is_open', static function (Challenge $challenges) {
                        if ($challenges->is_open == '0') {
                            $html = "<span class='badge badge-info'>Open</span>";
                        } elseif ($challenges->is_open == '1') {
                            $html = "<span class='badge badge-danger'>Close</span>";
                        } elseif ($challenges->is_open == '2') {
                            $html = "<span class='badge badge-success'>Completed</span>";
                        }

                        return $html;
                    })
                    ->addColumn('action', static function (Challenge $challenges) {
                        return '<a class="mr-10" href="'.route('challenge.edit', ['challenge' => $challenges->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" class="mr-10" href="'.route('challenge.assessment', ['assessment' => $challenges->id]).'"><i class="fas fa-calendar"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteChallenge(\''.route('challenge.destroy', ['challenge' => $challenges->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['status', 'is_open', 'action', 'DT_Row_Index'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
                ['data' => 'title', 'name' => 'title', 'title' => 'Challenge Title', 'width' => '65%'],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => 'User Name'],
                ['data' => 'is_open', 'name' => 'is_open', 'title' => 'Status', 'width' => '8%'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Published', 'width' => '8%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '8%'],
            ])->parameters([
                'order' => [[1, 'asc']],
            ]);

            return view('maestro.challenge.index', compact('html'));
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getLanguages();

            return view('maestro.challenge.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->createChallenge($request)) {
                return redirect()->route('challenge.index')->with('success', 'Challenge created successfully');
            }
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $challenge = $this->getChallengeById($id);
            if (!$challenge->exists) {
                return redirect()->route('challenge.index')->with(['error' => 'Challenge not found.']);
            }
            $languages = LanguageService::getLanguages();
            $challengeAssociatedItems = $this->getChallengeAssociatedItemsById($challenge);
            return view('maestro.challenge.edit', compact('languages', 'challenge', 'challengeAssociatedItems'));
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateChallengeById($id, $request)) {
                return redirect()->route('challenge.index')->with('success', 'Challenge Updated successfully');
            }
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Oops! Something went wrong. Please try again later. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($this->deleteChallengeById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Challenge deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Open challenge assessment edit page.
     */
    public function assessment(string $id)
    {
        try {
            $challenge = $this->getChallengeById($id);
            if (!$challenge->exists) {
                return redirect()->route('challenge.index')->with(['error' => 'Challenge not found.']);
            }
            $assessment = ChallengeAssessmentService::getAssessment($challenge->id);
            $criteria   = ChallengeAssessmentCriteriaService::getCriteria($challenge->id);

            return view('maestro.challenge.assessment', compact('assessment', 'challenge', 'criteria'));
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function assessmentStore(Request $request)
    {
        try {
            if ($this->storeUpdateAssessment($request)) {
                return redirect()->route('challenge.index')->with('success', 'Challenge Assessment saved successfully.');
            }
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Oops! Something went wrong. Please try again later. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
