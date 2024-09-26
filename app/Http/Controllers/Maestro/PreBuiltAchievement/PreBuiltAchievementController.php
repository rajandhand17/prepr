<?php

namespace App\Http\Controllers\Maestro\PreBuiltAchievement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\PreBuiltAchievement;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\PreBuiltAchievement\PreBuiltAchievementTrait;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class PreBuiltAchievementController extends Controller
{
    use PreBuiltAchievementTrait;


    public function index(Builder $builder, Request $request)
    {
        try {
            $achievement = PreBuiltAchievement::query();
            if ($request->ajax()) {
                $component = \Session::get('componentFilter');
                if ($component != 'all' && !empty($component)) {
                    $filterIds = PreBuiltAchievement::whereRaw('FIND_IN_SET(?, component_type)', $component)->pluck('id');
                    $achievement = $achievement->whereIn('id', $filterIds);
                }

                return DataTables::eloquent($achievement)
                    ->addColumn('action', static function (PreBuiltAchievement $achievementData) {
                        return '<a style="padding-left:10px" class="mr-10" href="'.route('pre-built-achievement.show', ['pre_built_achievement' => $achievementData->id]).'"><i class="fas fa-eye"></i></a><a style="padding-left:20px" class="mr-10" href="'.route('pre-built-achievement.edit', ['pre_built_achievement' => $achievementData->id]).'"><i class="fas fa-edit"></i></a> <a style="padding-left:20px" href="javascript:void(0)" onclick="deleteAchievement(\''.route('pre-built-achievement.destroy', ['pre_built_achievement' => $achievementData->id]).'\')"><i class="fas fa-trash" style="color: red;"></i></a>';
                    })
                    ->editColumn('achievement_image', static function (PreBuiltAchievement $achievementData) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('no-img.jpg').'";';

                        return "<img src='$achievementData->achievement_image' width='60px' ".$onerror.'>';
                    })
                    ->editColumn('component_type', static function (PreBuiltAchievement $achievementData) {
                        if (!empty($achievementData->component_type)) {
                            return ucwords(str_replace('_', ' ', $achievementData->component_type));
                        }
                    })
                    ->editColumn('checkmark', function (PreBuiltAchievement $achievementData) {
                        return '<input type="checkbox" name="check" class="select_ticket" value='.$achievementData->id.' data-id='.$achievementData->id.'>';
                    })
                    ->rawColumns(['achievement_image', 'action', 'DT_Row_Index', 'checkmark'])
                    ->addIndexColumn()
                    ->toJson();
            }

            // Prepare the columns for the DataTable
            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false, 'width' => '5%'],
            ];
            array_push($tableColumns, ['data' => 'checkmark', 'orderable' => false, 'searchable' => false, 'name' => 'id', 'title' => '<input type="checkbox" onClick="toggle(this)" id="select_all">']);
            array_push($tableColumns, ['data' => 'achievement_image', 'name' => 'achievement_image', 'title' => 'Image']);
            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Title'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'points', 'name' => 'points', 'title' => 'Points']);
            array_push($tableColumns, ['data' => 'component_type', 'name' => 'component_type', 'title' => 'Component', 'orderable' => false]);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'width' => '10%']);
            $html = $builder->columns($tableColumns);

            return view('maestro.pre-built-achievement.index', compact('html', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function setComponentForFilter(Request $request)
    {
        try {
            \Session::put('componentFilter', $request->component);

            return response()->json(['status' => 'success', 'message' => 'Component set successfully.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.pre-built-achievement.create', compact('languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($this->storeUpdatePreBuiltAchievement($request, '', 'create')) {
                return redirect()->route('pre-built-achievement.index')->with(['success' => 'Pre Built Achievement Added successfully.']);
            }

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $achievement = $this->findPreBuiltAchievement($id);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.pre-built-achievement.edit', compact('achievement', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($this->storeUpdatePreBuiltAchievement($request, $id, 'update')) {
                return redirect()->route('pre-built-achievement.index')->with(['success' => 'Pre Built Achievement updated successfully.']);
            }

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $achievement = $this->findPreBuiltAchievement($id);
            if (!empty($achievement)) {
                $this->deletePreBuiltAchievement($achievement);

                return response()->json(['status' => 'success', 'message' => 'Pre Built Achievement deleted successfully.']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $achievement = $this->findPreBuiltAchievement($id);
            if (!$achievement->exists) {
                return redirect()->route('pre-built-achievement.index')->with(['error' => 'Pre Built Achievement not found.']);
            }

            return view('maestro.pre-built-achievement.view', compact('achievement'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('pre-built-achievement.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->ids;

            if (!empty($ids)) {
                // Perform the deletion
                PreBuiltAchievement::whereIn('id', $ids)->delete();

                return response()->json(['success' => true, 'message' => 'Selected records have been deleted.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No records selected.']);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
