<?php

namespace App\Http\Controllers\Maestro\TrophyAwards;

use Illuminate\Support\Facades\Auth;
use App\Helpers\MixpanelHelper;
use App\Models\BadgeDetail;
use App\Models\UserPoint;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TrophyAwards;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\Event;
use App\Traits\Maestro\TrophyAwards\TrophyAwardsTrait;
use Illuminate\Support\Facades\DB;

/* -----------------------------------------------------------------------------------------
  @description: This controller is for handle TrophyAwards
  @functions: index, show, create, store, edit, update, delete
  ----------------------------------------------------------------------------------------- */

class TrophyAwardsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    use TrophyAwardsTrait;
    public function __construct()
    {
        $this->middleware('web');
    }
    /* -----------------------------------------------------------------------------------------
      @Description: Function for view all trophy awards
      @Output: Show all trophy awards on admin panel
      -------------------------------------------------------------------------------------------- */

    public function index(Builder $builder, Request $request)
    {
        try {
            $trophy_awards = TrophyAwards::latest();
            if (request()->ajax()) {
                return DataTables::eloquent($trophy_awards)
                    ->addIndexColumn()
                    ->editColumn('name', static function (TrophyAwards $trophy_awards) {
                        return $trophy_awards->name;
                    })
                    ->editColumn('description', static function (TrophyAwards $trophy_awards) {
                        return substr($trophy_awards->description, 0, 50) . '...';
                    })
                    ->editColumn('image', static function (TrophyAwards $trophy_awards) {
                        return "<img src ='" . asset($trophy_awards->image) . "' >";
                    })
                    ->addColumn('action', static function (TrophyAwards $trophy_awards) {
                        return '<a href="' . route('trophyawards.edit',  $trophy_awards->id) . '" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="' . $trophy_awards->id . '"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="deleteTrophyAward(\'' . route('trophyawards.destroy', $trophy_awards->id) . '\')"> <i class="fas fa-trash"></i></a>';
                   })
                    ->rawColumns(['action','DT_Row_Index'])
                    ->make(true);
            }

            $html = $builder->columns([
            ['data' => 'id', 'name' => 'DT_Row_Index', 'title' => 'S.No.', 'orderable' => false, 'searchable' => false],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'description', 'name' => 'description', 'title' => 'Description'],
            ['data' => 'points_gained', 'name' => 'points_gained', 'title' => 'Points Gained'],
            ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
            ])->parameters([
            'order' => [[ 1, 'asc' ]]
            ]);
            return view('maestro.trophy.index', compact('html'));
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for share view for create trophy awards
      @Output: return the view for create trophy awards
      -------------------------------------------------------------------------------------------- */

    public function create()
    {
        try {
            $status = [
                'active'   => 'Active',
                'inactive' => 'Inactive'
            ];
            $users = User::pluck('username', 'id');
            $awardedMembers = [];

            return view('maestro.trophy.create', compact('status', 'users', 'awardedMembers'));
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for save trophy awards
      @input: name, image, description, point, status
      @Output: save trophy awards in database
      -------------------------------------------------------------------------------------------- */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->createTrophyAwards($request)) {
                DB::commit();
                return redirect()->route('trophyawards.index')->with('success', 'Trophy Awards created successfully');
            }
            DB::rollback();
            return redirect()->route('trophyawards.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('trophyawards.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for show view of edit trophy awards
    @input: res_id, quiz_id
    @Output: returns trophy award's edit view
    -------------------------------------------------------------------------------------------- */
    public function edit($id)
    {
        try {
            $status = [
                'active'   => 'Active',
                'inactive' => 'Inactive'
            ];
           
            $awardedTrophies = TrophyAwards::find($id);

            // get awarded members
            $awardedMembers = explode(',', $awardedTrophies->user_id);

            $users = User::pluck('username', 'id');
            return view('maestro.trophy.edit', compact('awardedTrophies', 'users', 'status', 'awardedMembers'));
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('trophyawards.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for update trophy award
      @input: id, name, image, description, point, status
      @Output: update trophy award in database
      -------------------------------------------------------------------------------------------- */

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            if ($this->updateTrophyAwardsById($id,$request)) {
                DB::commit();
                return redirect()->route('trophyawards.index')->with('success', 'Trophy Award Updated successfully');
            }
            DB::rollback();
            return redirect()->route('trophyawards.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->route('trophyawards.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
      @Description: Function for delete trophy awards
      @input: id
      @Output: remove trophy awards from database
      -------------------------------------------------------------------------------------------- */

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteTrophyAwardsById($id)) {
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
