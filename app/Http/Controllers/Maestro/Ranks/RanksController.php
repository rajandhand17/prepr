<?php

namespace App\Http\Controllers\Maestro\Ranks;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Traits\Maestro\Rank\RankTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class RanksController extends Controller
{
    use RankTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder)
    {
        try {
            $rank = $this->getRank();
            if (request()->ajax()) {
                return DataTables::eloquent($rank)
                    ->addColumn('action', static function (Rank $rankData) {
                        return '<a style="padding-left:20px" class="mr-10" href="'.route('ranks.edit', ['rank' => $rankData->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteRank(\''.route('ranks.destroy', ['rank' => $rankData->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('status', static function (Rank $rankData) {
                        if ($rankData->status == 1) {
                            return 'Active';
                        } else {
                            return 'Not Active';
                        }
                    })
                    ->addIndexColumn()
                    ->toJson();
            }

            $languages = $this->getLanguage();
            $tableColumns = [
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false],
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
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Rank Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'point', 'name' => 'point', 'title' => 'Points', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.rank.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->getLanguage();
            $status = $this->getRankStatus();

            return view('maestro.rank.create', compact('languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateRank($request, '', 'create')) {
                DB::commit();

                return redirect()->route('ranks.index')->with(['success' => 'Rank Added successfully.']);
            }

            return redirect()->route('ranks.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('ranks.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->getLanguage();
            $rank = $this->findRank($id);
            $status = $this->getRankStatus();

            return view('maestro.rank.edit', compact('rank', 'languages', 'status'));
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdateRank($request, $id, 'update')) {
                DB::commit();

                return redirect()->route('ranks.index')->with(['success' => 'Rank updated successfully.']);
            }

            return redirect()->route('ranks.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('ranks.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $rank = $this->findRank($id);
            if (!empty($rank)) {
                $this->deleteRank($rank);
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Rank deleted successfully.']);
            }
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
