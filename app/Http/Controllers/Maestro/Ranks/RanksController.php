<?php

namespace App\Http\Controllers\Maestro\Ranks;

use App\Helpers\Maestro\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\Rank\RankTrait;
use Exception;
use Illuminate\Http\Request;
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
                            $html = "<span class='badge badge-success'>Active</span>";
                        } else {
                            $html = "<span class='badge badge-danger'>InActive</span>";
                        }

                        return $html;
                    })
                    ->editColumn('image', static function (Rank $rankData) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('no-img.jpg').'";';

                        return "<img src='".$rankData->image."' width='30px' ".$onerror.'>';
                    })
                    ->addIndexColumn()
                    ->rawColumns(['image', 'status', 'action', 'DT_Row_Index'])
                    ->toJson();
            }
            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false],
            ];
            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Rank Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'point', 'name' => 'point', 'title' => 'Points', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'image', 'name' => 'image', 'title' => 'Image', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.rank.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.rank.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->storeUpdateRank($request, '', 'create')) {
                return redirect()->route('ranks.index')->with(['success' => 'Rank Added successfully.']);
            }

            return redirect()->route('ranks.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $rank = $this->findRank($id);

            return view('maestro.rank.edit', compact('rank', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->storeUpdateRank($request, $id, 'update')) {
                return redirect()->route('ranks.index')->with(['success' => 'Rank updated successfully.']);
            }

            return redirect()->route('ranks.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            return redirect()->route('ranks.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $rank = $this->findRank($id);
            if (!empty($rank)) {
                $this->deleteRank($rank);

                return response()->json(['status' => 'success', 'message' => 'Rank deleted successfully.']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
