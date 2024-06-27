<?php

namespace App\Http\Controllers\Maestro\Challenge;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Services\Maestro\Category\CategoryService;
use App\Services\Maestro\User\UserService;
use App\Traits\Maestro\Challenge\ChallengeTrait;
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
            $challengeInfo = $this->getChallenge();
            if (!empty($challengeInfo)) {
                if ($request->ajax()) {
                    return DataTables::eloquent($challengeInfo)
                        ->editColumn('status', static function (Challenge $challengeInfo) {
                            switch ($challengeInfo->status){
                                case '0';
                                    $html = "<span class='badge badge-success'>Draft</span>";
                                    break;
                                case '1';
                                    $html = "<span class='badge badge-success'>Published</span>";
                                    break;
                                case '2';
                                    $html = "<span class='badge badge-success'>Archive</span>";
                                    break;
                            }
                            return $html;
                        })->editColumn('privacy', static function (Challenge $challengeInfo) {
                            switch ($challengeInfo->privacy){
                                case '0';
                                    $html = "Public";
                                    break;
                                case '1';
                                    $html = "Private";
                                    break;
                            }
                            return $html;
                        })->editColumn('category', static function (Challenge $challengeInfo) {
                            $categoryId=CategoryService::getCategoryById($challengeInfo->category_id);
                            return $categoryId->title;
                        })->editColumn('username', static function (Challenge $challengeInfo) {
                            $user=UserService::getUserById($challengeInfo->user_id);
                            return $user->username;
                        })
                        ->addColumn('action', static function (Challenge $challengeInfo) {
                            return '<a href="javascript:void(0)" onclick="deleteChallenge(\'' . route('challenge.destroy', ['challenge' => $challengeInfo->id]) . '\')"> <i class="fas fa-trash"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['status','category','username','action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', "width" => "5%", 'orderable' => false, 'searchable' => false],
                ['data' => 'title', 'name' => 'title', 'title' => 'Title', "width" => "5%"],
                ['data' => 'privacy', 'name' => 'privacy', 'title' => 'Privacy', "width" => "10%"],
                ['data' => 'username', 'name' => 'username', 'title' => 'Username', "width" => "10%"],
                ['data' => 'category', 'name' => 'category', 'title' => 'Category', "width" => "10%"],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', "width" => "10%"],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.challenge.index', compact('html'));
        }catch (\Exception $e) {
            return false;
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            if ($this->deleteChallengeById($id)) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Challenge deleted successfully']);
            }
            DB::rollback();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $challenge = $this->getChallengeById($id);
            if(!$challenge->exists){
                return redirect()->route('challenge.index')->with(['error' => 'Challenge not found.']);
            }
            $roles = $this->getAllRoles();
            $permissions = $this->getAllPermissions();
            $selected_role = $user->getRoles();
            $assigned_all_permission = $user->allPermissions()->pluck('id')->toArray();
            return view('maestro.users.edit', compact('user','permissions','assigned_all_permission','roles','selected_role'));
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with(['error' => 'Something want wrong.']);
        }
    }
}
