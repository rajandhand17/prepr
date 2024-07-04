<?php

namespace App\Http\Controllers\Maestro\Challenges;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Traits\Maestro\Challenge\ChallengeTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ChallengeController extends Controller
{
    use ChallengeTrait;

    public function __construct()
    {
        $this->middleware('web');
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
                            $html = 'Draft';
                        } elseif ($challenges->status == '1') {
                            $html = 'Published';
                        } elseif ($challenges->status == '2') {
                            $html = 'Archive';
                        }

                        return $html;
                    })
                    ->editColumn('is_open', static function (Challenge $challenges) {
                        if ($challenges->is_open == '0') {
                            $html = 'Open';
                        } elseif ($challenges->is_open == '1') {
                            $html = 'Close';
                        } elseif ($challenges->is_open == '2') {
                            $html = 'Completed';
                        }

                        return $html;
                    })
                    ->addColumn('action', static function (Challenge $challenges) {
                        return '<a class="mr-10" href="'.route('challenge.edit', ['challenge' => $challenges->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" class="mr-10" href="'.route('challenge.assessment', ['assessment' => $challenges->id]).'"><i class="fas fa-calendar"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteChallenge(\''.route('challenge.destroy', ['challenge' => $challenges->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->rawColumns(['icon', 'action', 'DT_Row_Index'])
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
            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->getLanguage();

            return view('maestro.challenge.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            return view('maestro.challenge.show');
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createChallenge($request)) {
                DB::commit();

                return redirect()->route('challenge.index')->with('success', 'Challenge created successfully');
            }
            DB::rollback();

            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
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
            $languages = $this->getLanguage();
            $challengeAssociatedItems = $this->getChallengeAssociatedItemsById($challenge);

            return view('maestro.challenge.edit', compact('languages', 'challenge', 'challengeAssociatedItems'));
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateChallengeById($id, $request)) {
                DB::commit();

                return redirect()->route('challenge.index')->with('success', 'Challenge Updated successfully');
            }
            DB::rollback();

            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteChallengeById($id)) {
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Challenge deleted successfully']);
            }
            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Something want wrong.']);
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
            $assessment = $this->getAssessment($challenge->id);
            $criteria = $this->getCriteria($challenge->id);
            return view('maestro.challenge.assessment', compact('assessment','challenge','criteria'));
        } catch (Exception $e) {
            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }
    public function assessmentStore(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateAssessment($request)) {
                DB::commit();
                return redirect()->route('challenge.index')->with('success', 'Challenge Assessment saved successfully.');
            }
            DB::rollback();

            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('challenge.index')->with(['error' => 'Something want wrong.']);
        }
    }
}
