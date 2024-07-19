<?php

namespace App\Http\Controllers\Maestro\ActivityAwards;

use App\Http\Controllers\Controller;
use App\Models\CommunityTrophy;
use App\Models\Language;
use App\Models\User;
use App\Traits\Maestro\CommunityTrophy\CommunityTrophyTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function __construct()
    {
        $this->middleware('web');
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for List all community trophies
      @Output: Show all community trophies on admin panel
      -------------------------------------------------------------------------------------------- */
    public function index(Builder $builder, Request $request)
    {
        try {
            // $trophys = User::query()->where('id','<>',Auth::id());
            $users = communityTrophy::query();
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
                        return '<a href="'.route('communitytrophy.edit', $trophy->id).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$trophy->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="deleteCommunityAward(\''.route('communitytrophy.destroy', $trophy->id).'\')"> <i class="fas fa-trash"></i></a>';
                    })
                    ->make(true);
            }

            $languages = Language::where('status', 1)->get();
            $tableColumns = [
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ];
            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName = 'name';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName = $columName.'_name';
                }
                $singleLangCol = ['data' => $columName, 'name' => $columName, 'title' => $single->name.' Name'];
                array_push($tableColumns, $singleLangCol);
            }
            array_push($tableColumns, ['data' => 'image', 'name' => 'image', 'title' => 'Image']);
            array_push($tableColumns, ['data' => 'points', 'name' => 'points', 'title' => 'Points']);
            array_push($tableColumns, ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]);
            $html = $builder->columns($tableColumns);

            return view('maestro.activityawards.communityTrophy.index', compact('html'));
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
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
            $user = User::find($request->id);

            return view('maestro.trophy.show', compact('user'));
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for share create view for trophy
      @Output: return the view for create community trophy
      -------------------------------------------------------------------------------------------- */
    public function create()
    {
        try {
            View::share('status', ['0' => 'Active', '1' => 'Deactive']);
            $languages = Language::where('status', 1)->get();

            return view('maestro.activityawards.communityTrophy.create', compact('languages'));
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
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
            DB::beginTransaction();
            if ($this->createCommunityTrophy($request)) {
                DB::commit();

                return redirect()->route('communitytrophy.index')->with('success', 'Trophy Awards created successfully');
            }
            DB::rollback();

            return redirect()->route('communitytrophy.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('communitytrophy.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for share edit view for trophy
      @input:id
      -------------------------------------------------------------------------------------------- */
    public function edit($id)
    {
        try {
            $languages = Language::where('status', 1)->get();
            View::share('status', ['0' => 'Active', '1' => 'Deactive']);
            View::share('title', 'Edit Trophy');
            $trophy = communityTrophy::find($id);

            return view('maestro.activityawards.communityTrophy.edit', compact('trophy', 'languages'));
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status'  => 'error',
            ]);
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
            DB::beginTransaction();
            if ($this->updateCommunityTrophyById($id, $request)) {
                DB::commit();

                return redirect()->route('communitytrophy.index')->with('success', 'Trophy Award Updated successfully');
            }
            DB::rollback();

            return redirect()->route('communitytrophy.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            dd($e);
            DB::rollback();

            return redirect()->route('communitytrophy.index')->with(['error' => 'Something went wrong.']);
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
            DB::beginTransaction();
            if ($this->deleteCommunityTrophyById($id)) {
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
