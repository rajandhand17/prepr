<?php

namespace App\Http\Controllers\Maestro\ActivityAwards;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\CommunityTrophy;
use App\Services\Maestro\LanguageService;
use App\Services\Maestro\UserService;
use App\Traits\Maestro\CommunityTrophy\CommunityTrophyTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

/* -----------------------------------------------------------------------------------------
  @description: This controller is for handle Community trophy
  @functions: index, show, create, store, edit, update, destroy
  ----------------------------------------------------------------------------------------- */

class CommunityTrophyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    use CommunityTrophyTrait;


    /* -----------------------------------------------------------------------------------------
      @Description: Function for List all community trophies
      @Output: Show all community trophies on admin panel
      -------------------------------------------------------------------------------------------- */
    public function index(Builder $builder, Request $request)
    {
        try {
            $users = $this->getCommunityTrophy();
            if (request()->ajax()) {
                return DataTables::eloquent($users)
                    ->editColumn('name', static function (communityTrophy $trophy) {
                        return $trophy->name;
                    })
                    ->editColumn('image', static function (communityTrophy $trophy) {
                        $onerror = 'onerror=this.onerror=null;this.src="'.asset('front/img/no-img.jpg').'";';

                        return "<img src ='".asset($trophy->image)."' width='60' ".$onerror.'>';
                    })->rawColumns(['image', 'action'])
                    ->addColumn('action', static function (communityTrophy $trophy) {
                        return '<a href="'.route('community-trophy.edit', $trophy->id).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$trophy->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="deleteCommunityAward(\''.route('community-trophy.destroy', $trophy->id).'\')"> <i class="fas fa-trash"></i></a>';
                    })
                    ->make(true);
            }

            $languages = LanguageService::getAllActiveLanguages();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ];
            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'name');

                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'image', 'name' => 'image', 'title' => 'Image']);
            array_push($tableColumns, ['data' => 'points', 'name' => 'points', 'title' => 'Points']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);
            $html = $builder->columns($tableColumns);

            return view('maestro.activity-awards.community-trophy.index', compact('html'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('community-trophy.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for show Trophy
      @input:id
      @Output: returns single trophy
      -------------------------------------------------------------------------------------------- */
    public function show(Request $request)
    {
        try {
            $user = UserService::getUserById($request->id);

            return view('maestro.trophy.show', compact('user'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('community-trophy.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for share create view for trophy
      @Output: return the view for create community trophy
      -------------------------------------------------------------------------------------------- */
    public function create()
    {
        try {
            $status = ['0' => 'Active', '1' => 'Deactive'];
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.activity-awards.community-trophy.create', compact('status', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('community-trophy.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for save trophy in database
      @input:name, image, points and required points array
      @Output: save trophy in database
      -------------------------------------------------------------------------------------------- */
    public function store(Request $request)
    {
        try {
            if ($this->createCommunityTrophy($request)) {
                return redirect()->route('community-trophy.index')->with('success', 'Trophy Awards created successfully');
            }

            return redirect()->route('community-trophy.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('community-trophy.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for share edit view for trophy
      @input:id
      -------------------------------------------------------------------------------------------- */
    public function edit($id)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            $status = ['0' => 'Active', '1' => 'Deactive'];
            $trophy = communityTrophy::find($id);

            return view('maestro.activity-awards.community-trophy.edit', compact('trophy', 'languages', 'status'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('community-trophy.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for update trophy in database
      @input:id, name, image, points and required points array
      @Output: update trophy in database
      -------------------------------------------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        try {
            if ($this->updateCommunityTrophyById($id, $request)) {
                return redirect()->route('community-trophy.index')->with('success', 'Trophy Award Updated successfully');
            }

            return redirect()->route('community-trophy.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->route('community-trophy.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for delete trophy
      @input: id
      @Output: delete trophy from database
      -------------------------------------------------------------------------------------------- */
    public function destroy($id)
    {
        try {
            if ($this->deleteCommunityTrophyById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }
}
