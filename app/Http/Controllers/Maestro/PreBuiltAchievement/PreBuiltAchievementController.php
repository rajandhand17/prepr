<?php

namespace App\Http\Controllers\Maestro\PreBuiltAchievement;

use App\Http\Controllers\Controller;
use App\Models\PreBuiltAchievement;
use App\Traits\Maestro\PreBuiltAchievement\PreBuiltAchievementTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class PreBuiltAchievementController extends Controller
{
    use PreBuiltAchievementTrait;

    public function __construct()
    {
        $this->middleware('auth-check');
    }

    public function index(Builder $builder)
    {
        try {
            $achievement = $this->getPreBuiltAchievement();
            if (request()->ajax()) {
                return DataTables::eloquent($achievement)
                    ->addColumn('action', static function (PreBuiltAchievement $achievementData) {
                        return '<a style="padding-left:20px" class="mr-10" href="'.route('pre-built-achievement.edit', ['pre_built_achievement' => $achievementData->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteAchievement(\''.route('pre-built-achievement.destroy', ['pre_built_achievement' => $achievementData->id]).'\')"><i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('image', static function (PreBuiltAchievement $achievementData) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('no-img.jpg').'";';

                        return "<img src='".asset($achievementData->image)."' width='30px' ".$onerror.'>';
                    })
                    ->editColumn('status', static function (PreBuiltAchievement $achievementData) {
                        if ($achievementData->status == '1') {
                            return 'Active';
                        } else {
                            return 'DeActive';
                        }
                    })
                    ->editColumn('component_type', static function (PreBuiltAchievement $achievementData) {
                        if (!empty($achievementData->component_type)) {
                            return ucwords(str_replace('_', ' ', $achievementData->component_type));
                        }
                    })
                    ->rawColumns(['image', 'action', 'DT_Row_Index'])
                    ->addIndexColumn()
                    ->toJson();
            }

            $languages = $this->getLanguage();
            $tableColumns = [
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ];
            array_push($tableColumns, ['data' => 'image', 'name' => 'image', 'title' => 'Image']);
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
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'points', 'name' => 'points', 'title' => 'Points']);
            array_push($tableColumns, ['data' => 'component_type', 'name' => 'component_type', 'title' => 'component']);
            array_push($tableColumns, ['data' => 'status', 'name' => 'status', 'title' => 'Status', 'width' => '10%']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.preBuiltAchievement.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = $this->getLanguage();

            return view('maestro.preBuiltAchievement.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdatePreBuiltAchievement($request, '', 'create')) {
                DB::commit();

                return redirect()->route('pre-built-achievement.index')->with(['success' => 'Pre Built Achievement Added successfully.']);
            }

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Something want wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->getLanguage();
            $achievement = $this->findPreBuiltAchievement($id);

            return view('maestro.preBuiltAchievement.edit', compact('achievement', 'languages'));
        } catch (Exception $e) {
            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->storeUpdatePreBuiltAchievement($request, $id, 'update')) {
                DB::commit();

                return redirect()->route('pre-built-achievement.index')->with(['success' => 'Pre Built Achievement updated successfully.']);
            }

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Something want wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Something want wrong.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $achievement = $this->findPreBuiltAchievement($id);
            if (!empty($achievement)) {
                $this->deletePreBuiltAchievement($achievement);
                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'PreBuiltAchievement deleted successfully.']);
            }
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'fail', 'message' => 'Something want wrong.']);
        }
    }
}
